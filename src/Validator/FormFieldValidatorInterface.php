<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\HttpFoundation\Request;

interface FormFieldValidatorInterface
{
    public function validatePresenceOrThrow(Request $request, string $fieldName): string;
}
