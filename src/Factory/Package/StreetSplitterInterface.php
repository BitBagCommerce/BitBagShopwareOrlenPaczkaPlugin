<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory\Package;

interface StreetSplitterInterface
{
    public function splitStreet(string $street): array;
}
