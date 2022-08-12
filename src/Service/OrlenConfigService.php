<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Service;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\ApiConfigNotFoundException;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class OrlenConfigService implements OrlenConfigServiceInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getApiConfig(?string $salesChannelId = null): OrlenApiConfig
    {
        $username = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.Username', $salesChannelId) ?: null;
        $password = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.Password', $salesChannelId) ?: null;
        $environment = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.Environment', $salesChannelId) ?: null;

        if (null === $username || null === $password || null === $environment) {
            throw new ApiConfigNotFoundException('api.credentialsDataNotFound');
        }

        return new OrlenApiConfig($username, $password, $environment);
    }
}
