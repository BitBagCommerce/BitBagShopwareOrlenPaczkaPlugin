<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\StreetCannotBeSplitException;

final class StreetSplitter implements StreetSplitterInterface
{
    public function splitStreet(string $street): array
    {
        if (!preg_match('/^(.+)\s(\d.*)/', $street, $streetAddress)) {
            throw new StreetCannotBeSplitException('order.order_address.invalid_street');
        }

        return $streetAddress;
    }
}
