<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Plugin;

use Shopware\Core\Framework\Context;

interface RuleConfiguratorInterface
{
    public function getRuleId(Context $context): string;
}
