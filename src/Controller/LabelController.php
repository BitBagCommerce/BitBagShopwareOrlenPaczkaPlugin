<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Controller;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\DocumentNotFoundException;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderAddressException;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\PackageException;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Finder\OrderFinderInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\OrderExtensionDataResolverInterface;
use OpenApi\Annotations as OA;
use Shopware\Core\Checkout\Document\DocumentEntity;
use Shopware\Core\Checkout\Order\Aggregate\OrderDelivery\OrderDeliveryEntity;
use Shopware\Core\Content\Media\MediaService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @RouteScope(scopes={"api"})
 */
final class LabelController
{
    private OrderFinderInterface $orderFinder;

    private OrderExtensionDataResolverInterface $orderExtensionDataResolver;

    private MediaService $mediaService;

    public function __construct(
        OrderFinderInterface $orderFinder,
        OrderExtensionDataResolverInterface $orderExtensionDataResolver,
        MediaService $mediaService
    ) {
        $this->orderFinder = $orderFinder;
        $this->orderExtensionDataResolver = $orderExtensionDataResolver;
        $this->mediaService = $mediaService;
    }

    /**
     * @OA\Get(
     *     path="/api/_action/bitbag-orlen-paczka-plugin/label/{orderId}",
     *     summary="Gets an Orlen Paczka package label for an order",
     *     operationId="show",
     *     tags={"Admin API", "Orlen Paczka"},
     *     @OA\Parameter(
     *         name="orderId",
     *         description="Identifier of the order the package should be generated for",
     *         @OA\Schema(type="string", pattern="^[0-9a-f]{32}$"),
     *         in="path",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Orlen Paczka package formatted as PDF",
     *         @OA\MediaType(
     *             mediaType="application/pdf",
     *             @OA\Schema(
     *                type="string",
     *                format="binary"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error while rendering the package label"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Not found"
     *     )
     * )
     * @Route("/api/_action/bitbag-orlen-paczka-plugin/label/{orderId}", name="api.action.bitbag_orlen_paczka_plugin.label", methods={"GET"})
     */
    public function show(string $orderId, Context $context): Response
    {
        $order = $this->orderFinder->getWithAssociations($orderId, $context);
        $orderExtensionData = $this->orderExtensionDataResolver->getData($order);
        if (null === $orderExtensionData['packageId']) {
            throw new PackageException('package.notFound');
        }

        $deliveries = $order->getDeliveries();
        if (null === $deliveries || 0 === $deliveries->count()) {
            throw new OrderAddressException('order.address.notFound');
        }

        /** @var OrderDeliveryEntity $delivery */
        $delivery = $deliveries->first();

        $shippingMethod = $delivery->getShippingMethod();
        if (null === $shippingMethod) {
            throw new OrderAddressException('order.address.shippingMethodNotFound');
        }

        $technicalName = $shippingMethod->getTranslated()['customFields']['technical_name'] ?? null;
        if (ShippingMethodPayloadFactoryInterface::SHIPPING_KEY !== $technicalName) {
            throw new OrderAddressException('order.address.shippingMethod.notOrlenPaczka');
        }

        $documents = $order->getDocuments();
        if (null === $documents || 0 === $documents->count()) {
            throw new DocumentNotFoundException('document.notFound');
        }

        /** @var DocumentEntity $document */
        $document = $documents->first();

        $media = $document->getDocumentMediaFile();
        if (null === $media) {
            throw new DocumentNotFoundException('document.mediaNotFound');
        }

        $packageId = $orderExtensionData['packageId'];
        $filename = sprintf('filename="label_%s.pdf"', $packageId);

        $response = new Response($this->mediaService->loadFile($media->getId(), $context));
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Content-Disposition', $filename);

        return $response;
    }
}
