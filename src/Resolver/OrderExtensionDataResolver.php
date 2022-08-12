<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\Order\OrderExtensionNotFoundException;
use BitBag\ShopwareOrlenPaczkaPlugin\Extension\Order\OrlenOrderExtension;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;
use Shopware\Core\Checkout\Order\OrderEntity;

final class OrderExtensionDataResolver implements OrderExtensionDataResolverInterface
{
    public function getPickupPointAddress(OrderEntity $order): PickupPointAddress
    {
        $orderExtension = $order->getExtension(OrlenOrderExtension::PROPERTY_KEY);
        if (null === $orderExtension) {
            throw new OrderExtensionNotFoundException('order.extension.notFound');
        }

        $data = $orderExtension->getVars()['data'];

        return new PickupPointAddress(
            $data['id'],
            $data['pickupPointPni'],
            $data['pickupPointCity'],
            $data['pickupPointName'],
            $data['pickupPointProvince'],
            $data['pickupPointStreet'],
            $data['pickupPointZipCode'],
        );
    }
}
