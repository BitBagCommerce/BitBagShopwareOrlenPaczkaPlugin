<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\Address;
use Shopware\Core\Checkout\Order\Aggregate\OrderAddress\OrderAddressEntity;

interface AddressFactoryInterface
{
    public function create(OrderAddressEntity $orderAddress, string $email): Address;
}
