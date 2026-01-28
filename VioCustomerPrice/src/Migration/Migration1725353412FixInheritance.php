<?php declare(strict_types=1);

namespace VioCustomerPrice\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * @internal
 */
#[Package('core')]
class Migration1725353412FixInheritance extends MigrationStep
{
    use InheritanceUpdaterTrait;

    public function getCreationTimestamp(): int
    {
        return 1725353412;
    }

    public function update(Connection $connection): void
    {
        $this->updateInheritance($connection, 'customer', 'customerPrices');
    }
}
