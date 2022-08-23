<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Config;

use BitBag\ShopwareOrlenPaczkaPlugin\Config\OrlenApiConfigService;
use BitBag\ShopwareOrlenPaczkaPlugin\Exception\InvalidApiConfigException;
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
            ->willReturnOnConsecutiveCalls('', 'password', 'production', '');

        $orlenConfigService = new OrlenApiConfigService($this->systemConfigService);

        $orlenConfigService->getApiConfig(null);
    }

    public function testGetConfig(): void
    {
        $this->systemConfigService
            ->method('getString')
            ->willReturnOnConsecutiveCalls('username', 'password', 'sandbox');
        $this->systemConfigService
            ->method('getInt')
            ->willReturn(123);

        $orlenConfigService = new OrlenApiConfigService($this->systemConfigService);

        $config = $orlenConfigService->getApiConfig(null);

        self::assertSame('username', $config->getUsername());
        self::assertSame('password', $config->getPassword());
        self::assertSame('sandbox', $config->getEnvironment());
        self::assertSame(123, $config->getOriginOffice());
    }
}
