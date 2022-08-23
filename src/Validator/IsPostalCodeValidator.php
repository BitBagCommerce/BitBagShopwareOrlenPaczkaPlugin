<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class IsPostalCodeValidator extends ConstraintValidator
{
    public const POST_CODE_REGEX = "/^(\d{2})(-\d{3})?$/i";

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsPostalCode) {
            throw new UnexpectedTypeException($constraint, IsPostalCode::class);
        }

        $isValid = $this->isValid($value);
        if (!$isValid) {
            $postalCode = trim(substr_replace($value, '-', 2, 0));
            if ($this->isValid($postalCode)) {
                return;
            }

            $this->context->buildViolation('order.address.postCodeInvalid')
                          ->addViolation();
        }
    }

    private function isValid(string $postalCode): bool
    {
        return (bool) preg_match(self::POST_CODE_REGEX, $postalCode);
    }
}
