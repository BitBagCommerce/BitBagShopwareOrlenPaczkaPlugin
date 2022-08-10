<?php

declare(strict_types=1);

namespace BitBagShopwareOrlenPaczkaPlugin\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1659360978OrderExtension extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1659360978;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `bitbag_orlen_order_extension` (
    `id` BINARY(16) NOT NULL,
    `pickup_point_pni` VARCHAR(10) NOT NULL,
    `pickup_point_city` VARCHAR(255) NOT NULL,
    `pickup_point_name` VARCHAR(255) NOT NULL,
    `pickup_point_province` VARCHAR(255) NOT NULL,
    `pickup_point_street` VARCHAR(255) NOT NULL,
    `pickup_point_zip_code` VARCHAR(255) NOT NULL,
    `order_id` BINARY(16) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
