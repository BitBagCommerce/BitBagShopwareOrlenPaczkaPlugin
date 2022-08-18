<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class PackageException extends ShopwareHttpException
{
    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__PACKAGE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}