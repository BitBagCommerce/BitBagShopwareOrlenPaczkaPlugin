<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Config;

use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

interface OrlenApiConfigServiceInterface
{
    public const SYSTEM_CONFIG_PREFIX = 'BitBagShopwareOrlenPaczkaPlugin.orlen.';

    public function getApiConfig(?string $salesChannelId): OrlenApiConfig;
}