<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory;

final class RulePayloadFactory implements RulePayloadFactoryInterface
{
    public function create(string $name): array
    {
        return [
            'name' => $name,
            'priority' => 100,
        ];
    }
}
