<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\PPClient\Client\PPClient;
use BitBagShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

interface PPClientResolverInterface
{
    public function resolve(OrlenApiConfig $config): PPClient;
}
