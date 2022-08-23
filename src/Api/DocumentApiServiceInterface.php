<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
