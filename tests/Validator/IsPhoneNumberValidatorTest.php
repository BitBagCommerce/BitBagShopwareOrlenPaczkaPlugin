<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Tests\Validator;

use BitBag\ShopwareOrlenPaczkaPlugin\Validator\IsPhoneNumber;
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
