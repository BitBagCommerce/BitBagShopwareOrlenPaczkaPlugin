<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Api;

use BitBag\PPClient\Client\PPClientInterface;
use BitBag\PPClient\Model\AddDeliveryResponseItem;
use BitBag\PPClient\Model\Packet;
use BitBag\PPClient\Model\Request\PocztexDeliveryRequest;
use BitBag\PPClient\Model\Request\SendEnvelopeRequest;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderAddressException;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderException;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\PackageException;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package\AddressFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package\PackageFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
use BitBag\ShopwareOrlenPaczkaPlugin\Resolver\ApiResolverInterface;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

final class PackageApiService implements PackageApiServiceInterface
{
    private AddressFactoryInterface $addressFactory;

    private PackageFactoryInterface $packageFactory;

    private ApiResolverInterface $apiResolver;

    private DocumentApiServiceInterface $documentApiService;

    public function __construct(
        AddressFactoryInterface $addressFactory,
        PackageFactoryInterface $packageFactory,
        ApiResolverInterface $apiResolver,
        DocumentApiServiceInterface $documentApiService
    ) {
        $this->addressFactory = $addressFactory;
        $this->packageFactory = $packageFactory;
        $this->apiResolver = $apiResolver;
        $this->documentApiService = $documentApiService;
    }

    public function createPackage(
        OrlenApiConfig $config,
        OrderEntity $order,
        Context $context
    ): AddDeliveryResponseItem {
        if (null === $config->getOriginOffice()) {
            throw new InvalidApiConfigException('config.emptyOriginOffice');
        }

        $orderAddress = null;

        $deliveries = $order->getDeliveries();
        if (null !== $deliveries) {
            $delivery = $deliveries->first();
            if (null !== $delivery) {
                $orderAddress = $delivery->getShippingOrderAddress();
            }
        }

        if (null === $orderAddress) {
            throw new OrderAddressException('order.address.notFound');
        }

        $email = null;

        $orderCustomer = $order->getOrderCustomer();

        if (null !== $orderCustomer) {
            $email = $orderCustomer->getEmail();
        }

        if (null === $email) {
            throw new OrderException('order.emailNotFound');
        }

        $address = $this->addressFactory->create($orderAddress, $email);
        $package = $this->packageFactory->create(
            $order,
            $address,
            $context
        );

        $client = $this->apiResolver->getClient($config);
        $client->clearEnvelope();

        $shipmentRequest = new PocztexDeliveryRequest();
        $shipmentRequest->setPackages([$package]);
        $shipment = $client->addPocztexDelivery($shipmentRequest);

        $firstPackageResponse = $shipment->getAddDeliveryResponseItems()[0];
        if ([] !== $firstPackageResponse->getErrors()) {
            throw new PackageException($firstPackageResponse->getErrors()[0]->getErrorDesc());
        }

        $this->documentApiService->uploadOrderLabel(
            $package->getGuid(),
            $order->id,
            $order->orderNumber,
            $client,
            $context
        );

        $this->sendPackage(
            $package->getGuid(),
            $config->getOriginOffice(),
            $client
        );

        return $firstPackageResponse;
    }

    private function sendPackage(
        string $packageGuid,
        int $originOffice,
        PPClientInterface $client
    ): void {
        $packet = new Packet();
        $packet->setGuid($packageGuid);

        $sendEnvelopeRequest = new SendEnvelopeRequest();
        $sendEnvelopeRequest->setPacket($packet);
        $sendEnvelopeRequest->setParcelOriginOffice($originOffice);

        $sendEnvelope = $client->sendEnvelope($sendEnvelopeRequest);
        if ([] !== $sendEnvelope->getErrors()) {
            throw new PackageException($sendEnvelope->getErrors()[0]->getErrorDesc());
        }
    }
}
