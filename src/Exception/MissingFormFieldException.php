<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;
use Symfony\Component\HttpFoundation\Response;

final class MissingFormFieldException extends ShopwareHttpException
{
    public function __construct(string $fieldName)
    {
        parent::__construct($fieldName);
    }

    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__MISSING_FORM_FIELD';
    }

    public function getStatusCode(): int
    {
        return Response::HTTP_BAD_REQUEST;
    }
}
