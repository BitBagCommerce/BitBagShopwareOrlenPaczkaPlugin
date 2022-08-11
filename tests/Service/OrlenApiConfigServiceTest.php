<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Test\Service;

use BitBagShopwareOrlenPaczkaPlugin\Config\OrlenApiConfigService;
use BitBagShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class OrlenApiConfigServiceTest extends TestCase
{
    private SystemConfigService $systemConfigService;

    protected function setUp(): void
    {
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
    }

    public function testMissingData(): void
    {
        $this->expectException(InvalidApiConfigException::class);

        $this->systemConfigService
            ->method('getString')
            ->willReturnOnConsecutiveCalls('', 'password', 'production');

        $orlenConfigService = new OrlenApiConfigService($this->systemConfigService);

        $orlenConfigService->getApiConfig(null);
    }

    public function testGetConfig(): void
    {
        $this->systemConfigService
            ->method('getString')
            ->willReturnOnConsecutiveCalls('username', 'password', 'sandbox');

        $orlenConfigService = new OrlenApiConfigService($this->systemConfigService);

        $config = $orlenConfigService->getApiConfig(null);

        self::assertSame('username', $config->getUsername());
        self::assertSame('password', $config->getPassword());
        self::assertSame('sandbox', $config->getEnvironment());
    }
}
