<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Test\Factory;

use BitBag\ShopwareOrlenPaczkaPlugin\Factory\RulePayloadFactory;
use PHPUnit\Framework\TestCase;

class RulePayloadFactoryTest extends TestCase
{
    public const NAME = 'rule-factory';

    public const PAYMENT_METHOD_ID = '123';

    public function testCreate(): void
    {
        self::assertEquals(
            [
                'name' => self::NAME,
                'priority' => 100,
                'conditions' => [
                    [
                        'type' => 'paymentMethod',
                        'value' => [
                            'paymentMethodIds' => [self::PAYMENT_METHOD_ID],
                            'operator' => '!=',
                        ],
                    ],
                ],
            ],
            (new RulePayloadFactory())->create(self::NAME, self::PAYMENT_METHOD_ID)
        );
    }
}
