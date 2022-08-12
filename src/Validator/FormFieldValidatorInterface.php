<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\HttpFoundation\Request;

interface FormFieldValidatorInterface
{
    public function validatePresenceOrThrow(Request $request, string $fieldName): string;
}
