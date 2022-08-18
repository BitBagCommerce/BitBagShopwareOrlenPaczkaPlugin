<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Resolver;

use BitBag\PPClient\Client\PPClient;
use BitBag\ShopwareOrlenPaczkaPlugin\Model\OrlenApiConfig;

interface ApiResolverInterface
{
    public function getClient(OrlenApiConfig $config): PPClient;
}
