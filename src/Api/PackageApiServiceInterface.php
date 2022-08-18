<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Api;

use BitBag\PPClient\Model\AddDeliveryResponseItem;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
use Shopware\Core\Checkout\Order\OrderEntity;
use Shopware\Core\Framework\Context;

interface PackageApiServiceInterface
{
    public function createPackage(
        OrlenApiConfig $config,
        OrderEntity $order,
        Context $context
    ): AddDeliveryResponseItem;
}
