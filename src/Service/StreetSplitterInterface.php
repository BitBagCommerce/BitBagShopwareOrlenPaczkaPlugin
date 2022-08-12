<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Service;

interface StreetSplitterInterface
{
    public function splitStreet(string $street): array;
}
