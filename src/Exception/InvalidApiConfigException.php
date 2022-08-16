<?php

declare(strict_types=1);

namespace BitBag\ShopwareOrlenPaczkaPlugin\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

final class InvalidApiConfigException extends ShopwareHttpException
{
    public function __construct()
    {
        parent::__construct('config.invalid');
    }

    public function getErrorCode(): string
    {
        return 'BITBAG_ORLEN_PACZKA_PLUGIN__INVALID_API_CONFIG';
    }
}
