<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\HttpFoundation\Request;

interface FormFieldValidatorInterface
{
    public function validatePresenceOrThrow(
        Request $request,
        string $fieldName,
        bool $allowEmpty = false
    ): string;
}
