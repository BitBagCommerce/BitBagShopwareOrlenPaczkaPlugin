<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Config;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;
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
        $originOffice = $this->systemConfigService->getInt(self:: SYSTEM_CONFIG_PREFIX . 'originOffice', $salesChannelId) ?: null;

        if (null === $username || null === $password || null === $environment) {
            throw new InvalidApiConfigException('config.invalid');
        }

        return new OrlenApiConfig(
            $username,
            $password,
            $environment,
            $originOffice
        );
    }
}
