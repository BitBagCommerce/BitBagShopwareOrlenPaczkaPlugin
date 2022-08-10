<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Guid;

final class Guid
{
    public static function generate(): string
    {
        return strtoupper(md5(uniqid((string) random_int(32, 32), true)));
    }
}
