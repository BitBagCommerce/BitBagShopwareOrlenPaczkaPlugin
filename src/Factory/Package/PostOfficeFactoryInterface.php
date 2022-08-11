<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\PostOffice;
use BitBagShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;

interface PostOfficeFactoryInterface
{
    public function create(PickupPointAddress $pickupPointAddress): PostOffice;
}
