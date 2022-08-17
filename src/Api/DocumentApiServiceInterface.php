<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Api;

use BitBag\PPClient\Client\PPClientInterface;
use Shopware\Core\Framework\Context;

interface DocumentApiServiceInterface
{
    public function uploadOrderLabel(
        string $packageGuid,
        string $orderId,
        string $orderNumber,
        PPClientInterface $client,
        Context $context
    ): void;
}
