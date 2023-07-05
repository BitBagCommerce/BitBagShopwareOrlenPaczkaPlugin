<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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

        $data = $orderExtension->getVars();

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

    public function getData(OrderEntity $order): array
    {
        $orderExtension = $order->getExtension(OrlenOrderExtension::PROPERTY_KEY);
        if (null === $orderExtension) {
            throw new OrderExtensionNotFoundException('order.extension.notFound');
        }

        return $orderExtension->getVars();
    }
}
