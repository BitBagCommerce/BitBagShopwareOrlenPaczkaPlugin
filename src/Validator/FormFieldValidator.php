<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Validator;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\MissingFormFieldException;
use Symfony\Component\HttpFoundation\Request;

final class FormFieldValidator implements FormFieldValidatorInterface
{
    public function validatePresenceOrThrow(
        Request $request,
        string $fieldName,
        bool $allowEmpty = false
    ): string {
        /** @var string|null $field */
        $field = $request->request->get($fieldName);

        if (null !== $field && ($allowEmpty || '' !== $field)) {
            return $field;
        }

        throw new MissingFormFieldException($fieldName);
    }
}
