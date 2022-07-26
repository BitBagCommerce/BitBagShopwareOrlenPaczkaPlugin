<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Plugin;

use BitBagShopwareOrlenPaczkaPlugin\Factory\ShippingMethodPayloadFactoryInterface;
use BitBagShopwareOrlenPaczkaPlugin\Finder\ShippingMethodFinderInterface;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

final class ShippingMethodConfigurator implements ShippingMethodConfiguratorInterface
{
    private ShippingMethodFinderInterface $shippingMethodFinder;

    private ShippingMethodPayloadFactoryInterface $shippingMethodPayloadFactory;

    private EntityRepositoryInterface $shippingMethodRepository;

    public function __construct(
        ShippingMethodFinderInterface $shippingMethodFinder,
        ShippingMethodPayloadFactoryInterface $shippingMethodPayloadFactory,
        EntityRepositoryInterface $shippingMethodRepository
    ) {
        $this->shippingMethodFinder = $shippingMethodFinder;
        $this->shippingMethodPayloadFactory = $shippingMethodPayloadFactory;
        $this->shippingMethodRepository = $shippingMethodRepository;
    }

    public function createShippingMethod(string $ruleId, Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchIdsByShippingKey($context);

        if (0 !== $shippingMethod->getTotal()) {
            return;
        }

        $orlenShippingMethod = $this->shippingMethodPayloadFactory->create(
            ShippingMethodPayloadFactoryInterface::SHIPPING_KEY,
            $ruleId,
            $context
        );

        $this->shippingMethodRepository->create([$orlenShippingMethod], $context);
    }

    public function toggleActiveShippingMethod(bool $active, Context $context): void
    {
        $shippingMethod = $this->shippingMethodFinder->searchByShippingKey($context);

        if (0 !== $shippingMethod->getTotal()) {
            /** @var ShippingMethodEntity $firstShippingMethod */
            $firstShippingMethod = $shippingMethod->first();

            $this->shippingMethodRepository->update([
                [
                    'id' => $firstShippingMethod->getId(),
                    'active' => $active,
                ],
            ], $context);
        }
    }
}
