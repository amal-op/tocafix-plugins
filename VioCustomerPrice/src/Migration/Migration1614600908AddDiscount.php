<?php declare(strict_types=1);

namespace VioCustomerPrice\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1614600908AddDiscount extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1_614_600_908;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $sql = <<< SQL
ALTER TABLE vio_customer_price ADD COLUMN `discount` DOUBLE NULL;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
