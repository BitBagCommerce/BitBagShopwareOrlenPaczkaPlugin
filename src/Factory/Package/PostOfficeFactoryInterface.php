<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\PostOffice;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;

interface PostOfficeFactoryInterface
{
    public function create(PickupPointAddress $pickupPointAddress): PostOffice;
}
