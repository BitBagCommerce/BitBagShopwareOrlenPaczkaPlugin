<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class NoRequestException extends ShopwareHttpException
{
    public function __construct()
    {
        parent::__construct('Request stack does not contain a main request');
    }

    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__NO_REQUEST_EXCEPTION';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}
