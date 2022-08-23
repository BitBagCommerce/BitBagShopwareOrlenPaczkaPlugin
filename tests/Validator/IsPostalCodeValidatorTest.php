<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
