<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\ShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;
use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderExtensionDataResolverInterface
{
    public function getPickupPointAddress(OrderEntity $order): PickupPointAddress;

    public function getData(OrderEntity $order): array;
}
