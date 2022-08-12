<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Factory;

use BitBag\ShopwareOrlenPaczkaPlugin\Finder\DeliveryTimeFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

final class ShippingMethodPayloadFactory implements ShippingMethodPayloadFactoryInterface
{
    private DeliveryTimeFinderInterface $deliveryTimeFinder;

    private DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory;

    private EntityRepository $deliveryTimeRepository;

    public function __construct(
        DeliveryTimeFinderInterface $deliveryTimeFinder,
        DeliveryTimePayloadFactoryInterface $createDeliveryTimeFactory,
        EntityRepository $deliveryTimeRepository
    ) {
        $this->deliveryTimeFinder = $deliveryTimeFinder;
        $this->createDeliveryTimeFactory = $createDeliveryTimeFactory;
        $this->deliveryTimeRepository = $deliveryTimeRepository;
    }

    public function create(
        string $name,
        string $ruleId,
        Context $context
    ): array {
        $currencyId = $context->getCurrencyId();

        $orlenShippingMethod = [
            'name' => $name,
            'active' => true,
            'description' => $name,
            'taxType' => 'auto',
            'translated' => [
                'name' => $name,
            ],
            'customFields' => [
                'technical_name' => $name,
            ],
            'availabilityRuleId' => $ruleId,
            'prices' => [
                [
                    'ruleId' => $ruleId,
                    'calculation' => 1,
                    'quantityStart' => 1,
                    'currencyPrice' => [
                        $currencyId => [
                            'net' => 0.0,
                            'gross' => 0.0,
                            'linked' => false,
                            'currencyId' => $currencyId,
                        ],
                    ],
                ],
            ],
        ];

        $deliveryId = $this->deliveryTimeFinder->getDeliveryTimeIds($context)->firstId();

        if (null === $deliveryId) {
            $this->deliveryTimeRepository->create([$this->createDeliveryTimeFactory->create()], $context);

            $deliveryId = $this->deliveryTimeFinder->getDeliveryTimeIds($context)->firstId();
        }

        $orlenShippingMethod['deliveryTimeId'] = $deliveryId;

        return $orlenShippingMethod;
    }
}
