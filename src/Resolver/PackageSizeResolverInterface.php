<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Resolver;

interface PackageSizeResolverInterface
{
    public function resolve(
        int $depth,
        int $height,
        int $width,
        int $weight
    ): string;
}
