<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Finder;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;

interface DeliveryTimeFinderInterface
{
    public function getDeliveryTimeIds(Context $context): IdSearchResult;
}
