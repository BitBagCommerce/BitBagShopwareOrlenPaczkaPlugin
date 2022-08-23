<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Factory;

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
