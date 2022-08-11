<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Exception\Order;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class OrderAddressException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__ORDER_ADDRESS';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
