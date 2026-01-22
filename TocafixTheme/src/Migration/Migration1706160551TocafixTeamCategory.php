<?php declare(strict_types=1);

namespace TocafixTheme\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1706160551TocafixTeamCategory extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1706160551;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
CREATE TABLE IF NOT EXISTS `tocafix_team_category` (
    `id`                    BINARY(16)                              NOT NULL,
    `version_id`            BINARY(16)                              NOT NULL,
    `active`                TINYINT(1)   DEFAULT 1                  NOT NULL,
    `created_at`            DATETIME(3)                             NOT NULL,
    `updated_at`            DATETIME(3)                             NULL,
    PRIMARY KEY (`id`, `version_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
