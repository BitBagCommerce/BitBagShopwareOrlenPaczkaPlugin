<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start.
 * You can find more information about us on https://bitbag.io and write us an email on hello@bitbag.io.
 */

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
