<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Validator;

use BitBag\ShopwareOrlenPaczkaPlugin\Validator\IsPostalCode;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class IsPostalCodeValidatorTest extends TestCase
{
    public function testValidateCorrectPostalCode(): void
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        self::assertEquals(
            0,
            $validator->validate('02-495', new IsPostalCode())->count()
        );
    }

    public function testValidateIncorrectPostalCode(): void
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        self::assertEquals(
            1,
            $validator->validate('002495', new IsPostalCode())->count()
        );
    }
}
