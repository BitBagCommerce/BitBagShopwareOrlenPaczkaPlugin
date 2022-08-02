<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Test\Factory;

use BitBagShopwareOrlenPaczkaPlugin\Factory\RulePayloadFactory;
use PHPUnit\Framework\TestCase;

class RulePayloadFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $name = 'rule-factory';

        self::assertEquals(
            [
                'name' => $name,
                'priority' => 100,
            ],
            (new RulePayloadFactory())->create($name)
        );
    }
}
