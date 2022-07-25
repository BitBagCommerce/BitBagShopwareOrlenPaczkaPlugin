<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Config;

use BitBagShopwareOrlenPaczkaPlugin\Exception\MissingApiConfigException;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
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
        // TODO: Change credentials structure
        $organizationId = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.OrganizationId', $salesChannelId) ?: null;
        $accessToken = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.AccessToken', $salesChannelId) ?: null;
        $environment = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.Environment', $salesChannelId) ?: null;

        if (null === $organizationId || null === $accessToken || null === $environment) {
            throw new MissingApiConfigException('api.credentialsDataNotFound');
        }

        return new OrlenApiConfig($organizationId, $accessToken, $environment);
    }
}
