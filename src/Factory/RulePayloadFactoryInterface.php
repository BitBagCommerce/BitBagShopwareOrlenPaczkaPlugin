<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory;

interface RulePayloadFactoryInterface
{
    public const ALWAYS_VALID = 'Always valid (Default)';

    public function create(string $name): array;
}
