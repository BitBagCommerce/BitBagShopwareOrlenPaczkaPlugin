<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Test\Factory;

use BitBag\ShopwareOrlenPaczkaPlugin\Factory\RulePayloadFactory;
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
            (new RulePayloadFactory())->create($name, '')
        );
    }
}
