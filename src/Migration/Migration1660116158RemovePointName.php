<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1660116158RemovePointName extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1660116158;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `bitbag_orlen_order_extension`
DROP COLUMN `point_name`;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
