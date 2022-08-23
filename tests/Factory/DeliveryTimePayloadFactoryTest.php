<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Factory;

use BitBag\ShopwareOrlenPaczkaPlugin\Factory\DeliveryTimePayloadFactory;
use PHPUnit\Framework\TestCase;

class DeliveryTimePayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $factory = new DeliveryTimePayloadFactory();

        self::assertEquals(
            [
                'name' => '1-3 days',
                'min' => 1,
                'max' => 3,
                'unit' => 'day',
            ],
            $factory->create()
        );
    }
}
