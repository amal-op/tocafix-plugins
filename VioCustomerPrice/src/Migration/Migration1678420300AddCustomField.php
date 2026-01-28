<?php declare(strict_types=1);

namespace VioCustomerPrice\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1678420300AddCustomField extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1_678_420_300;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        $sql = <<< SQL
        ALTER TABLE vio_customer_price ADD COLUMN `custom_fields` JSON NULL;
        SQL;
        $connection->executeStatement($sql);

        $sql = <<< SQL
ALTER TABLE vio_customer_price ADD CONSTRAINT `json.vio_customer_price.custom_fields` CHECK (JSON_VALID(`custom_fields`))
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
