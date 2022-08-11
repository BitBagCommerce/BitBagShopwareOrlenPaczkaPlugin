<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Config;

use BitBagShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class OrlenApiConfigService implements OrlenApiConfigServiceInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getApiConfig(?string $salesChannelId): OrlenApiConfig
    {
        $username = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . 'username', $salesChannelId) ?: null;
        $password = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . 'password', $salesChannelId) ?: null;
        $environment = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . 'environment', $salesChannelId) ?: null;

        if (null === $username || null === $password || null === $environment) {
            throw new InvalidApiConfigException();
        }

        return new OrlenApiConfig($username, $password, $environment);
    }
}
