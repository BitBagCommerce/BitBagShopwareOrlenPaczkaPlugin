<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class InvalidZipCodeException extends ShopwareHttpException
{
    public function __construct(?string $zipCode)
    {
        if (null === $zipCode) {
            parent::__construct('Zip code is missing');

            return;
        }

        parent::__construct(
            \sprintf('Provided zip code is invalid', $zipCode)
        );
    }

    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__INVALID_ZIP_CODE';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
