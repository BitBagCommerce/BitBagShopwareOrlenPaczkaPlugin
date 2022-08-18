<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrderCustomFieldModel;
use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderCustomFieldResolverInterface
{
    public function resolve(OrderEntity $order): OrderCustomFieldModel;
}
