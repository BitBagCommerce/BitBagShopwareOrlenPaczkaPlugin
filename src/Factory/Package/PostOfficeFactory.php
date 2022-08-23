<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

use BitBag\PPClient\Model\PostOffice;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\PickupPointAddress;

final class PostOfficeFactory implements PostOfficeFactoryInterface
{
    public function create(PickupPointAddress $pickupPointAddress): PostOffice
    {
        $postOffice = new PostOffice();
        $postOffice->setId($pickupPointAddress->getPni());
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
