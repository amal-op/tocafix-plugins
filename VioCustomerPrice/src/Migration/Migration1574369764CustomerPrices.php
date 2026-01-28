<?php declare(strict_types=1);

namespace VioCustomerPrice\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1574369764CustomerPrices extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1_574_369_764;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $sql = <<< SQL
CREATE TABLE IF NOT EXISTS `vio_customer_price` (
    `id` BINARY(16) NOT NULL,
    `customer_id` BINARY(16) NOT NULL,
    `product_id` BINARY(16) NOT NULL,
    `product_version_id` BINARY(16) NOT NULL,
    `quantity_start` INTEGER NOT NULL,
    `quantity_end` INTEGER NULL,
    `price` DOUBLE NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`),
    KEY `fk.vio_customer_price.customer_id` (`customer_id`),
    KEY `fk.vio_customer_price.product_id` (`product_id`,`product_version_id`),
    CONSTRAINT `fk.vio_customer_price.customer_id` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.vio_customer_price.product_id` FOREIGN KEY (`product_id`,`product_version_id`) REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
    UNIQUE `vio_customer_price.unique`(`customer_id`, `product_id`, `product_version_id`, `quantity_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
