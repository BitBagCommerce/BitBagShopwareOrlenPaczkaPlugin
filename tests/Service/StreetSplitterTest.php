<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Service;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\StreetCannotBeSplitException;
use BitBag\ShopwareOrlenPaczkaPlugin\Service\StreetSplitter;
use BitBag\ShopwareOrlenPaczkaPlugin\Service\StreetSplitterInterface;
use PHPUnit\Framework\TestCase;

class StreetSplitterTest extends TestCase
{
    private StreetSplitterInterface $splitStreetProvider;

    public const SINGLE_STREET = 'Testowa 12';

    public const TRIPLE_STREET = 'Os. Smoka Wawelskiego 12B';

    public const NUMERIC_STREET = '1 Maja 12A';

    public const NO_HOUSE_NUMBER = 'Testowa';

    public const SPECIAL_CHARACTERS = '"Dywizjonu" 303 90B';

    public const SPECIAL_CHARACTERS_INSIDE = 'Tadeusza "Zośki" Zawadzkiego 2137';

    public const DOUBLE_STREET = 'Braci Załuskich 7';

    protected function setUp(): void
    {
        $this->splitStreetProvider = new StreetSplitter();
    }

    public function testSingleStreetName(): void
    {
        $street = $this->splitStreetProvider->splitStreet(self::SINGLE_STREET);

        self::assertEquals([self::SINGLE_STREET, 'Testowa', 12], $street);
    }

    public function testDoubleStreetName(): void
    {
        $street = $this->splitStreetProvider->splitStreet(self::DOUBLE_STREET);

        self::assertEquals([self::DOUBLE_STREET, 'Braci Załuskich', '7'], $street);
    }

    public function testTripleStreetName(): void
    {
        $street = $this->splitStreetProvider->splitStreet(self::TRIPLE_STREET);

        self::assertEquals([self::TRIPLE_STREET, 'Os. Smoka Wawelskiego', '12B'], $street);
    }

    public function testNumericStreetName(): void
    {
        $street = $this->splitStreetProvider->splitStreet(self::NUMERIC_STREET);

        self::assertEquals([self::NUMERIC_STREET, '1 Maja', '12A'], $street);
    }

    public function testStreetWithoutHouseNumber(): void
    {
        $this->expectException(StreetCannotBeSplitException::class);

        $this->splitStreetProvider->splitStreet(self::NO_HOUSE_NUMBER);
    }

    public function testStreetWithSpecialCharacters(): void
    {
        $street = $this->splitStreetProvider->splitStreet(self::SPECIAL_CHARACTERS);
        $street2 = $this->splitStreetProvider->splitStreet(self::SPECIAL_CHARACTERS_INSIDE);

        self::assertEquals([self::SPECIAL_CHARACTERS, '"Dywizjonu" 303', '90B'], $street);
        self::assertEquals([self::SPECIAL_CHARACTERS_INSIDE, 'Tadeusza "Zośki" Zawadzkiego', '2137'], $street2);
    }
}
