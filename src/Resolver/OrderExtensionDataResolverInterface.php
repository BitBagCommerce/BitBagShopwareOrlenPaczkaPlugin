<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Resolver;

use BitBagShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;
use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderExtensionDataResolverInterface
{
    public function getPickupPointAddress(OrderEntity $order): PickupPointAddress;
}
