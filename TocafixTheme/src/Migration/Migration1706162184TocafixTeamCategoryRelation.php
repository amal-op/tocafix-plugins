<?php declare(strict_types=1);

namespace TocafixTheme\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1706162184TocafixTeamCategoryRelation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1706162184;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
CREATE TABLE IF NOT EXISTS `tocafix_team_category_relation` (
    `tocafix_team_id`                    BINARY(16)      NOT NULL,
    `tocafix_team_category_id`           BINARY(16)      NOT NULL,
    `tocafix_team_category_version_id`   BINARY(16)      NOT NULL,
    `created_at`                DATETIME(3)     NOT NULL,
    PRIMARY KEY (`tocafix_team_id`, `tocafix_team_category_id`, `tocafix_team_category_version_id`),
    CONSTRAINT `fk.tocafix_team_category_relation.team_id` FOREIGN KEY (`tocafix_team_id`)
    REFERENCES `tocafix_team` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk.tocafix_team_category_relation.cat_id__cat_version_id` FOREIGN KEY (`tocafix_team_category_id`, `tocafix_team_category_version_id`)
    REFERENCES `tocafix_team_category` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
