<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Service;

use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

interface OrlenApiConfigServiceInterface
{
    public const SYSTEM_CONFIG_PREFIX = 'BitBagShopwareOrlenPaczkaPlugin.orlen.';

    public function getApiConfig(?string $salesChannelId): OrlenApiConfig;

    public function updateApiConfig(OrlenApiConfig $config, ?string $salesChannelId): void;
}
