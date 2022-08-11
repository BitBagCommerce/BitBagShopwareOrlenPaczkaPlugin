<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Validator;

use BitBagShopwareOrlenPaczkaPlugin\Exception\MissingFormFieldException;
use Symfony\Component\HttpFoundation\Request;

final class FormFieldValidator implements FormFieldValidatorInterface
{
    public function validatePresenceOrThrow(Request $request, string $fieldName): string
    {
        /** @var string|null $field */
        $field = $request->request->get($fieldName);

        if (null !== $field && '' !== $field) {
            return $field;
        }

        throw new MissingFormFieldException($fieldName);
    }
}
