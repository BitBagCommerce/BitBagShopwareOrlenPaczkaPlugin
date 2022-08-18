<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory;

final class RulePayloadFactory implements RulePayloadFactoryInterface
{
    public function create(string $name, string $paymentMethodId): array
    {
        return [
            'name' => $name,
            'priority' => 100,
            'conditions' => [
                [
                    'type' => 'paymentMethod',
                    'value' => [
                        'paymentMethodIds' => [$paymentMethodId],
                        'operator' => '!=',
                    ],
                ],
            ],
        ];
    }
}
