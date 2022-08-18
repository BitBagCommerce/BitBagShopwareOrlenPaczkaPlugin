<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Plugin;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\RuleNotFoundException;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\RulePayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Finder\CashOnDeliveryPaymentMethodFinderInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Finder\RuleFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

final class RuleConfigurator implements RuleConfiguratorInterface
{
    private RuleFinderInterface $ruleFinder;

    private CashOnDeliveryPaymentMethodFinderInterface $cashOnDeliveryPaymentMethodFinder;

    private RulePayloadFactoryInterface $rulePayloadFactory;

    private EntityRepository $ruleRepository;

    public function __construct(
        RuleFinderInterface $ruleFinder,
        CashOnDeliveryPaymentMethodFinderInterface $cashOnDeliveryPaymentMethodFinder,
        RulePayloadFactoryInterface $rulePayloadFactory,
        EntityRepository $ruleRepository
    ) {
        $this->ruleFinder = $ruleFinder;
        $this->rulePayloadFactory = $rulePayloadFactory;
        $this->ruleRepository = $ruleRepository;
        $this->cashOnDeliveryPaymentMethodFinder = $cashOnDeliveryPaymentMethodFinder;
    }

    public function getRuleId(Context $context): string
    {
        $ruleName = RulePayloadFactoryInterface::DISABLE_PAYMENT_CASH_ON_DELIVERY;

        $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
        if (0 === $rule->getTotal()) {
            $paymentMethodCahOnDelivery = $this->cashOnDeliveryPaymentMethodFinder->find($context);

            $paymentMethodId = $paymentMethodCahOnDelivery->firstId();
            if (null !== $paymentMethodId) {
                $rule = $this->rulePayloadFactory->create($ruleName, $paymentMethodId);

                $this->ruleRepository->create([$rule], $context);

                $rule = $this->ruleFinder->getRuleIdsByName($ruleName, $context);
            }
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
