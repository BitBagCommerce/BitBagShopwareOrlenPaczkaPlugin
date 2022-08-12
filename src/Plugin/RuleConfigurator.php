<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Plugin;

use BitBag\ShopwareOrlenPaczkaPlugin\Exception\RuleNotFoundException;
use BitBag\ShopwareOrlenPaczkaPlugin\Factory\RulePayloadFactoryInterface;
use BitBag\ShopwareOrlenPaczkaPlugin\Finder\RuleFinderInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;

final class RuleConfigurator implements RuleConfiguratorInterface
{
    private RuleFinderInterface $ruleFinder;

    private RulePayloadFactoryInterface $rulePayloadFactory;

    private EntityRepository $ruleRepository;

    public function __construct(
        RuleFinderInterface $ruleFinder,
        RulePayloadFactoryInterface $rulePayloadFactory,
        EntityRepository $ruleRepository
    ) {
        $this->ruleFinder = $ruleFinder;
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
