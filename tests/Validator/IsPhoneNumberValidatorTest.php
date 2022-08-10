<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Tests\Validator;

use BitBagShopwareOrlenPaczkaPlugin\Validator\IsPhoneNumber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

final class IsPhoneNumberValidatorTest extends TestCase
{
    public function testValidateCorrectPhoneNumber(): void
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        self::assertEquals(
            0,
            $validator->validate('500-000-000', new IsPhoneNumber())->count()
        );
    }

    public function testValidateIncorrectPhoneNumber(): void
    {
        $validator = Validation::createValidatorBuilder()->getValidator();

        self::assertEquals(
            1,
            $validator->validate('12345678900', new IsPhoneNumber())->count()
        );
    }
}
