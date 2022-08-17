<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Finder;

use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

interface OrderFinderInterface
{
    public function getWithAssociations(string $orderId, Context $context): OrderEntity;

    public function getWithAssociationsByOrdersIds(array $ordersIds, Context $context): EntitySearchResult;
}
