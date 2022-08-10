<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface OrderCustomFieldValidatorInterface
{
    public function validate(array $data): ConstraintViolationListInterface;
}
