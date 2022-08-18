<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Api;

use BitBag\PPClient\Client\PPClientInterface;
use BitBag\PPClient\Model\Request\LabelRequest;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\LabelException;
use Shopware\Core\Checkout\Document\DocumentConfiguration;
use Shopware\Core\Checkout\Document\DocumentService;
use Shopware\Core\Content\Media\DataAbstractionLayer\MediaRepositoryDecorator;
use Shopware\Core\Content\Media\Exception\DuplicatedMediaFileNameException;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

final class DocumentApiService implements DocumentApiServiceInterface
{
    public const TEMP_NAME = 'document-import-';

    public const MEDIA_DIR = '/public/media/';

    public const MEDIA_FOLDER = 'Document';

    private DocumentService $documentService;

    private MediaService $mediaService;

    private FileSaver $fileSaver;

    private EntityRepository $documentRepository;

    private MediaRepositoryDecorator $mediaRepository;

    public function __construct(
        DocumentService $documentService,
        MediaService $mediaService,
        FileSaver $fileSaver,
        EntityRepository $documentRepository,
        MediaRepositoryDecorator $mediaRepository
    ) {
        $this->documentService = $documentService;
        $this->mediaService = $mediaService;
        $this->fileSaver = $fileSaver;
        $this->documentRepository = $documentRepository;
        $this->mediaRepository = $mediaRepository;
    }

    public function uploadOrderLabel(
        string $packageGuid,
        string $orderId,
        string $orderNumber,
        PPClientInterface $client,
        Context $context
    ): void {
        $labelRequest = new LabelRequest();
        $labelRequest->setGuid($packageGuid);

        $label = $client->getLabel($labelRequest);
        if ([] !== $label->getErrors()) {
            throw new LabelException($label->getErrors()[0]->getErrorDesc());
        }

        $labelPdfContent = $label->getAddressLabels()[0]->getPdfContent();

        $fileName = "bitbag_shopware_orlen_paczka_plugin_$orderNumber";
        $filenameWithExtension = $fileName . '.pdf';

        file_put_contents($filenameWithExtension, $labelPdfContent);

        $fileSize = filesize($filenameWithExtension);

        $mediaId = null;

        try {
            $mediaFile = new MediaFile(
                $filenameWithExtension,
                'application/pdf',
                'pdf',
                $fileSize
            );
            $mediaId = $this->mediaService->createMediaInFolder(self::MEDIA_FOLDER, $context, false);
            $this->fileSaver->persistFileToMedia(
                $mediaFile,
                $fileName,
                $mediaId,
                $context
            );
        } catch (DuplicatedMediaFileNameException $e) {
            $this->mediaCleanup($mediaId, $context);

            unlink($filenameWithExtension);

            throw $e;
        } catch (\Exception $e) {
            $this->mediaCleanup($mediaId, $context);

            unlink($filenameWithExtension);

            throw $e;
        }

        $createdDocument = $this->documentService->create(
            $orderId,
            'delivery_note',
            'pdf',
            new DocumentConfiguration(),
            $context
        );

        $this->documentRepository->update([
            [
                'id' => $createdDocument->getId(),
                'documentMediaFileId' => $mediaId,
            ],
        ], $context);

        unlink($filenameWithExtension);
    }

    private function mediaCleanup(?string $mediaId, Context $context): void
    {
        if (null === $mediaId) {
            return;
        }

        $this->mediaRepository->delete([['id' => $mediaId]], $context);
    }
}
