<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Model;

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
