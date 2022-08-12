<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\Validator\ConstraintViolationListInterface;

interface OrderCustomFieldValidatorInterface
{
    public function validate(array $data): ConstraintViolationListInterface;
}
