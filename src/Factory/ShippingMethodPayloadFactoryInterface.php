<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory;

use Shopware\Core\Framework\Context;

interface ShippingMethodPayloadFactoryInterface
{
    public const SHIPPING_KEY = 'Orlen Paczka';

    public function create(
        string $name,
        string $ruleId,
        Context $context
    ): array;
}
