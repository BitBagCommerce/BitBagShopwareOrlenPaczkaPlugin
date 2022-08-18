<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\ShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;
use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderExtensionDataResolverInterface
{
    public function getPickupPointAddress(OrderEntity $order): PickupPointAddress;

    public function getData(OrderEntity $order): array;
}
