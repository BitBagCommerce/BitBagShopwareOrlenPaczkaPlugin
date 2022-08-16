<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Config;

use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

interface OrlenApiConfigServiceInterface
{
    public const SYSTEM_CONFIG_PREFIX = 'BitBagShopwareOrlenPaczkaPlugin.orlen.';

    public function getApiConfig(?string $salesChannelId): OrlenApiConfig;
}
