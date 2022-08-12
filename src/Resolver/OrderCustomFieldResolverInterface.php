<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrderCustomFieldModel;
use Shopware\Core\Checkout\Order\OrderEntity;

interface OrderCustomFieldResolverInterface
{
    public const PACKAGE_DETAILS_KEY = 'bitbag_shopware_orlen_paczka_plugin_package_details';

    public function resolve(OrderEntity $order): OrderCustomFieldModel;
}
