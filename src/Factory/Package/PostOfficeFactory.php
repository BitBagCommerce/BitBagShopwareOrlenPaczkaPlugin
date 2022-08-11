<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\PostOffice;
use BitBagShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;

final class PostOfficeFactory implements PostOfficeFactoryInterface
{
    public function create(PickupPointAddress $pickupPointAddress): PostOffice
    {
        $postOffice = new PostOffice();
        $postOffice->setId($pickupPointAddress->getId());
        $postOffice->setPrefix('URZAD');
        $postOffice->setName($pickupPointAddress->getName());
        $postOffice->setPostalNetwork('ORLEN');
        $postOffice->setProvince($pickupPointAddress->getProvince());
        $postOffice->setCounty($pickupPointAddress->getCity());
        $postOffice->setPlace('');
        $postOffice->setCity($pickupPointAddress->getCity());
        $postOffice->setPrintoutName('ORLEN');
        $postOffice->setStreet($pickupPointAddress->getStreet());
        $postOffice->setHouseNumber('');
        $postOffice->setFlatNumber('');
        $postOffice->setPostCode($pickupPointAddress->getPostCode());
        $postOffice->setSMSnotification(false);

        return $postOffice;
    }
}
