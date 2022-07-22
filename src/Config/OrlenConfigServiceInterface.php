<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Config;

use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

interface OrlenConfigServiceInterface
{
    public const SYSTEM_CONFIG_PREFIX = 'BitBagShopwareOrlenPaczkaPlugin.orlen';

    public function getInPostApiConfig(?string $salesChannelId = null): OrlenApiConfig;
}
