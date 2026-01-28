<?php declare(strict_types=1);

namespace VioCustomerPrice\Migration;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1591973412AddInheritanceColumn extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1_591_973_412;
    }

    /**
     * @throws Exception
     */
    public function update(Connection $connection): void
    {
        // drop column, because in preveius versions the colum wasn't droped
        $column = $connection->executeQuery(
            'select * from information_schema.columns where table_schema = :table_schema and table_name = \'product\' and column_name = \'customerPrices\'',
            ['table_schema' => $connection->getDatabase()]
        )
            ->fetchOne();
        if ($column === 'customerPrices') {
            $connection->executeStatement('ALTER TABLE product DROP COLUMN customerPrices;');
        }
        $this->updateInheritance($connection, 'product', 'customerPrices');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
