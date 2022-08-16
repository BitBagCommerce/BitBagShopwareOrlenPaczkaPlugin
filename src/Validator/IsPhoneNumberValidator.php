<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class IsPhoneNumberValidator extends ConstraintValidator
{
    public const PHONE_NUMBER_REGEX = "/(?:(?:\+|00)[0-9]{1,3})?(\d{9,12})/";

    public const PHONE_NUMBER_LENGTH = 9;

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof IsPhoneNumber) {
            throw new UnexpectedTypeException($constraint, IsPhoneNumber::class);
        }

        $phoneNumber = str_replace(['+48', '+', '-', ' '], '', (string) $value);
        if ('' === $phoneNumber) {
            $this->context->buildViolation('bitbag.shopware_poczta_polska_app.order.address.phone_number_empty')
                         ->addViolation();

            return;
        }

        preg_match(self::PHONE_NUMBER_REGEX, $phoneNumber, $phoneNumberMatches);
        if ([] === $phoneNumberMatches) {
            $this->context->buildViolation('bitbag.shopware_poczta_polska_app.order.address.phone_number_invalid')
                          ->addViolation();

            return;
        }

        $phoneNumberLength = strlen($phoneNumberMatches[0]);
        if (self::PHONE_NUMBER_LENGTH !== $phoneNumberLength) {
            $this->context->buildViolation('bitbag.shopware_poczta_polska_app.order.address.phone_number_invalid')
                          ->addViolation();
        }
    }
}
