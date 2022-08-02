<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class ApiConfigNotFoundException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__API_CONFIG_NOT_FOUND_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}