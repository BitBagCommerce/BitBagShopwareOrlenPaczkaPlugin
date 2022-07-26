<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Plugin;

use Shopware\Core\Framework\Context;

interface CustomFieldSetConfiguratorInterface
{
    public function createCustomFieldSetForPackageDetails(Context $context): void;
}
