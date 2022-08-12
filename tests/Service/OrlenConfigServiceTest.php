<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Test\Service;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\ApiConfigNotFoundException;
use BitBag\ShopwareOrlenPaczkaPlugin\Service\OrlenConfigService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SystemConfig\SystemConfigService;

final class OrlenConfigServiceTest extends TestCase
{
    private SystemConfigService $systemConfigService;

    protected function setUp(): void
    {
        $this->systemConfigService = $this->createMock(SystemConfigService::class);
    }

    public function testMissingData(): void
    {
        $this->expectException(ApiConfigNotFoundException::class);

        $this->systemConfigService
            ->method('getString')
            ->willReturnOnConsecutiveCalls('', 'password', 'production');

        $orlenConfigService = new OrlenConfigService($this->systemConfigService);

        $orlenConfigService->getApiConfig();
    }

    public function testGetConfig(): void
    {
        $this->systemConfigService
            ->method('getString')
            ->willReturnOnConsecutiveCalls('username', 'password', 'sandbox');

        $orlenConfigService = new OrlenConfigService($this->systemConfigService);

        $config = $orlenConfigService->getApiConfig();

        self::assertSame('username', $config->getUsername());
        self::assertSame('password', $config->getPassword());
        self::assertSame('sandbox', $config->getEnvironment());
    }
}
