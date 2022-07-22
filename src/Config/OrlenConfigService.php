<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Config;

use BitBagShopwareOrlenPaczkaPlugin\Exception\ApiDataException;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class OrlenConfigService implements OrlenConfigServiceInterface
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function getInPostApiConfig(?string $salesChannelId = null): OrlenApiConfig
    {
        $organizationId = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostOrganizationId', $salesChannelId) ?: null;
        $accessToken = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostAccessToken', $salesChannelId) ?: null;
        $environment = $this->systemConfigService->getString(self:: SYSTEM_CONFIG_PREFIX . '.inPostEnvironment', $salesChannelId) ?: null;

        if (null === $organizationId || null === $accessToken || null === $environment) {
            throw new ApiDataException('api.credentialsDataNotFound');
        }

        return new OrlenApiConfig($organizationId, $accessToken, $environment);
    }
}
