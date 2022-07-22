<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Plugin;

use BitBagShopwareOrlenPaczkaPlugin\Exception\RuleNotFoundException;
use BitBagShopwareOrlenPaczkaPlugin\Factory\RulePayloadFactoryInterface;
use BitBagShopwareOrlenPaczkaPlugin\Finder\CashOnDeliveryPaymentMethodFinderInterface;
use BitBagShopwareOrlenPaczkaPlugin\Finder\RuleFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;

final class RuleConfigurator implements RuleConfiguratorInterface
{
    private RuleFinderInterface $ruleFinder;

    private CashOnDeliveryPaymentMethodFinderInterface $cashOnDeliveryPaymentMethodFinder;

    private RulePayloadFactoryInterface $rulePayloadFactory;

    private EntityRepositoryInterface $ruleRepository;

    public function __construct(
        RuleFinderInterface $ruleFinder,
        CashOnDeliveryPaymentMethodFinderInterface $cashOnDeliveryPaymentMethodFinder,
        RulePayloadFactoryInterface $rulePayloadFactory,
        EntityRepositoryInterface $ruleRepository
    ) {
        $this->ruleFinder = $ruleFinder;
        $this->cashOnDeliveryPaymentMethodFinder = $cashOnDeliveryPaymentMethodFinder;
        $this->rulePayloadFactory = $rulePayloadFactory;
        $this->ruleRepository = $ruleRepository;
    }

    public function getRuleId(Context $context): string
    {
        $ruleName = RulePayloadFactoryInterface::ALWAYS_VALID;
        $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
        if (0 === $rule->getTotal()) {
            $rule = $this->rulePayloadFactory->create($ruleName);

            $this->ruleRepository->create([$rule], $context);

            $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
        }

        if (0 === $rule->getTotal()) {
            throw new RuleNotFoundException('rule.notFound');
        }

        $ruleId = $rule->firstId();

        if (null === $ruleId) {
            throw new RuleNotFoundException('rule.notFound');
        }

        return $ruleId;
    }
}
