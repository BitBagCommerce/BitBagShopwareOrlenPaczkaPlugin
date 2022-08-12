<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

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
