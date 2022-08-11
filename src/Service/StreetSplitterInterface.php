<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Service;

interface StreetSplitterInterface
{
    public function splitStreet(string $street): array;
}
