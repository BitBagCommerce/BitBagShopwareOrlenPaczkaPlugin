<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1660115808OrderExtensionFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1659360979;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
ALTER TABLE `bitbag_orlen_order_extension`
    ADD `pickup_point_pni` VARCHAR(10) NOT NULL,
    ADD `pickup_point_city` VARCHAR(255) NOT NULL,
    ADD `pickup_point_name` VARCHAR(255) NOT NULL,
    ADD `pickup_point_province` VARCHAR(255) NOT NULL,
    ADD `pickup_point_street` VARCHAR(255) NOT NULL,
    ADD `pickup_point_zip_code` VARCHAR(255) NOT NULL;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
