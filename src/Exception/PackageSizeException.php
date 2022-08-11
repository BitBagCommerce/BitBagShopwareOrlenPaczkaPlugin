<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class PackageSizeException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__PACKAGE_SIZE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
