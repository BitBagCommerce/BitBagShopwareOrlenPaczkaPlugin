<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\PPClient\Model\PocztexPackageSizeEnum;
use BitBagShopwareOrlenPaczkaPlugin\Exception\PackageSizeException;

class PackageSizeResolver implements PackageSizeResolverInterface
{
    public function resolve(
        int $depth,
        int $height,
        int $width,
        int $weight
    ): string {
        if (PocztexPackageSizeEnum::MAX_HEIGHT_S >= $height &&
            PocztexPackageSizeEnum::MAX_DEPTH_S >= $depth &&
            PocztexPackageSizeEnum::MAX_WIDTH_S >= $width &&
            PocztexPackageSizeEnum::MAX_WEIGHT_S >= $weight
        ) {
            return PocztexPackageSizeEnum::S;
        }

        if (PocztexPackageSizeEnum::MAX_HEIGHT_M >= $height &&
            PocztexPackageSizeEnum::MAX_DEPTH_M >= $depth &&
            PocztexPackageSizeEnum::MAX_WIDTH_M >= $width &&
            PocztexPackageSizeEnum::MAX_WEIGHT_M >= $weight
        ) {
            return PocztexPackageSizeEnum::M;
        }

        if (PocztexPackageSizeEnum::MAX_HEIGHT_L >= $height &&
            PocztexPackageSizeEnum::MAX_DEPTH_L >= $depth &&
            PocztexPackageSizeEnum::MAX_WIDTH_L >= $width &&
            PocztexPackageSizeEnum::MAX_WEIGHT_L >= $weight
        ) {
            return PocztexPackageSizeEnum::L;
        }

        if (PocztexPackageSizeEnum::MAX_HEIGHT_XL >= $height &&
            PocztexPackageSizeEnum::MAX_DEPTH_XL >= $depth &&
            PocztexPackageSizeEnum::MAX_WIDTH_XL >= $width &&
            PocztexPackageSizeEnum::MAX_WEIGHT_XL >= $weight
        ) {
            return PocztexPackageSizeEnum::XL;
        }

        if (PocztexPackageSizeEnum::MAX_DEPTH_2XL < $depth) {
            throw new PackageSizeException('package.depthTooLarge');
        }

        $packageDimensions = $depth + $height + $width;
        if (PocztexPackageSizeEnum::MAX_DIMENSIONS_2XL < $packageDimensions) {
            throw new PackageSizeException('package.tooLarge');
        }

        return PocztexPackageSizeEnum::DOUBLE_XL;
    }
}
