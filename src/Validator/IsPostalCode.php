<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Validator;

use Symfony\Component\Validator\Constraint;

final class IsPostalCode extends Constraint
{
    public string $message = '';
}
