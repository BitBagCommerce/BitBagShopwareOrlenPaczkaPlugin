<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Api;

use BitBag\PPClient\Client\PPClientInterface;
use BitBag\PPClient\Model\Request\LabelRequest;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\LabelException;
use Doctrine\DBAL\Connection;
use Shopware\Core\Checkout\Document\Aggregate\DocumentBaseConfig\DocumentBaseConfigEntity;
use Shopware\Core\Checkout\Document\DocumentConfiguration;
use Shopware\Core\Checkout\Document\DocumentConfigurationFactory;
use Shopware\Core\Checkout\Document\DocumentIdStruct;
use Shopware\Core\Checkout\Document\Exception\InvalidDocumentGeneratorTypeException;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Content\Media\Exception\DuplicatedMediaFileNameException;
use Shopware\Core\Content\Media\File\FileSaver;
use Shopware\Core\Content\Media\File\MediaFile;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Uuid\Uuid;

final class DocumentApiService implements DocumentApiServiceInterface
{
    public const MEDIA_FOLDER = 'Document';

    private MediaService $mediaService;

    private FileSaver $fileSaver;

    private EntityRepository $documentRepository;

    private EntityRepository $mediaRepository;

    private EntityRepository $documentConfigRepository;

    private EntityRepository $orderRepository;

    private Connection $connection;

    public function __construct(
        MediaService $mediaService,
        FileSaver $fileSaver,
        EntityRepository $documentRepository,
        EntityRepository $mediaRepository,
        EntityRepository $documentConfigRepository,
        EntityRepository $orderRepository,
        Connection $connection
    ) {
        $this->mediaService = $mediaService;
        $this->fileSaver = $fileSaver;
        $this->documentRepository = $documentRepository;
        $this->mediaRepository = $mediaRepository;
        $this->documentConfigRepository = $documentConfigRepository;
        $this->orderRepository = $orderRepository;
        $this->connection = $connection;
    }

    public function uploadOrderLabel(
        string $packageGuid,
        OrderEntity $order,
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

        $fileName = "bitbag_shopware_orlen_paczka_plugin_{$order->getOrderNumber()}";
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

        $createdDocument = $this->createDeliveryNoteDocument(
            $order,
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

    private function createDeliveryNoteDocument(
        OrderEntity $order,
        Context $context
    ): DocumentIdStruct {
        $documentTypeName = 'delivery_note';
        $fileType = 'pdf';

        $documentTypeId = $this->getDocumentTypeIdByName($documentTypeName);

        if (null === $documentTypeId) {
            throw new InvalidDocumentGeneratorTypeException($documentTypeName);
        }

        $documentConfiguration = $this->getConfiguration(
            $context,
            $documentTypeId,
            $order
        );

        $orderVersionId = $this->orderRepository->createVersion($order->getId(), $context, 'document');

        $documentId = Uuid::randomHex();
        $deepLinkCode = Random::getAlphanumericString(32);
        $this->documentRepository->create(
            [
                [
                    'id' => $documentId,
                    'documentTypeId' => $documentTypeId,
                    'fileType' => $fileType,
                    'orderId' => $order->getId(),
                    'orderVersionId' => $orderVersionId,
                    'config' => $documentConfiguration->jsonSerialize(),
                    'static' => false,
                    'deepLinkCode' => $deepLinkCode,
                ],
            ],
            $context
        );

        return new DocumentIdStruct($documentId, $deepLinkCode);
    }

    private function getDocumentTypeIdByName(string $documentType): ?string
    {
        $id = $this->connection->fetchOne(
            'SELECT LOWER(HEX(id)) as id FROM document_type WHERE technical_name = :technicalName',
            ['technicalName' => $documentType]
        );

        return $id ?: null;
    }

    private function getConfiguration(
        Context $context,
        string $documentTypeId,
        OrderEntity $order,
        ): DocumentConfiguration {
        $specificConfiguration = [];
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('documentTypeId', $documentTypeId));
        $criteria->addAssociation('logo');
        $criteria->addFilter(new EqualsFilter('global', true));

        /** @var DocumentBaseConfigEntity $globalConfig */
        $globalConfig = $this->documentConfigRepository->search($criteria, $context)->first();

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('documentTypeId', $documentTypeId));
        $criteria->addAssociation('logo');
        $criteria->addFilter(new EqualsFilter('salesChannels.salesChannelId', $order->getSalesChannelId()));
        $criteria->addFilter(new EqualsFilter('salesChannels.documentTypeId', $documentTypeId));

        /** @var DocumentBaseConfigEntity $salesChannelConfig */
        $salesChannelConfig = $this->documentConfigRepository->search($criteria, $context)->first();

        return DocumentConfigurationFactory::createConfiguration($specificConfiguration, $globalConfig, $salesChannelConfig);
    }
}
