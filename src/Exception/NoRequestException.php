<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

final class NoRequestException extends ShopwareHttpException
{
    public function __construct()
    {
        parent::__construct('');
    }

    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__NO_REQUEST';
    }
}
