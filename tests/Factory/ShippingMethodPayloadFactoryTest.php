<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Factory;

use BitBag\ShopwareOrlenPaczkaPlugin\Factory\DeliveryTimePayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\ShippingMethodPayloadFactory;
use BitBag\ShopwareOrlenPaczkaPlugin\Finder\DeliveryTimeFinderInterface;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Uuid\Uuid;

class ShippingMethodPayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $deliveryTimeFinder = $this->createMock(DeliveryTimeFinderInterface::class);

        $deliveryTimePayloadFactory = $this->createMock(DeliveryTimePayloadFactoryInterface::class);

        $deliveryTimeRepository = $this->createMock(EntityRepository::class);

        $context = $this->createMock(Context::class);

        $factory = new ShippingMethodPayloadFactory(
            $deliveryTimeFinder,
            $deliveryTimePayloadFactory,
            $deliveryTimeRepository
        );

        $ruleId = Uuid::randomHex();

        $deliveryTimeId = Uuid::randomHex();

        $deliveryTimeFinder->expects(self::once())
            ->method('getDeliveryTimeIds')
            ->willReturn(
                new IdSearchResult(
                    1,
                    ['data' => ['primaryKey' => $deliveryTimeId, 'data' => ['id' => $deliveryTimeId]]],
                    new Criteria(),
                    $context
                )
            );

        $shippingMethodName = 'shipping-method';

        $currencyId = $context->getCurrencyId();

        self::assertEquals(
            [
                'name' => $shippingMethodName,
                'active' => true,
                'description' => $shippingMethodName,
                'taxType' => 'auto',
                'translated' => [
                    'name' => $shippingMethodName,
                ],
                'customFields' => [
                    'technical_name' => $shippingMethodName,
                ],
                'availabilityRuleId' => $ruleId,
                'deliveryTimeId' => $deliveryTimeId,
                'prices' => [
                    [
                        'ruleId' => $ruleId,
                        'calculation' => 1,
                        'quantityStart' => 1,
                        'currencyPrice' => [
                            $currencyId => [
                                'net' => 0.0,
                                'gross' => 0.0,
                                'linked' => false,
                                'currencyId' => $currencyId,
                            ],
                        ],
                    ],
                ],
            ],
            $factory->create($shippingMethodName, $ruleId, $context)
        );
    }
}
