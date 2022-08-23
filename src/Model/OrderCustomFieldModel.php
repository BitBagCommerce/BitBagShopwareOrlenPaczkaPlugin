<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Model;

final class OrderCustomFieldModel
{
    private int $depth;

    private int $height;

    private int $width;

    private string $packageContents;

    private string $plannedShippingDate;

    public function __construct(
        int $depth,
        int $height,
        int $width,
        string $packageContents,
        string $plannedShippingDate
    ) {
        $this->depth = $depth;
        $this->height = $height;
        $this->width = $width;
        $this->packageContents = $packageContents;
        $this->plannedShippingDate = $plannedShippingDate;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getPackageContents(): string
    {
        return $this->packageContents;
    }

    public function getPlannedShippingDate(): string
    {
        return $this->plannedShippingDate;
    }
}
