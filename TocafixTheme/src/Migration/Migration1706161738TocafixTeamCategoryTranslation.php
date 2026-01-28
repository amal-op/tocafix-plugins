<?php declare(strict_types=1);

namespace TocafixTheme\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1706161738TocafixTeamCategoryTranslation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1706161738;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `tocafix_team_category_translation` (
              `tocafix_team_category_id` BINARY(16) NOT NULL,
              `tocafix_team_category_version_id` BINARY(16) NOT NULL,
              `language_id` BINARY(16) NOT NULL,
              `name` VARCHAR(255),
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`tocafix_team_category_id`, `language_id`, `tocafix_team_category_version_id`),
              CONSTRAINT `fk.team_category.team_category_id` FOREIGN KEY (`tocafix_team_category_id`, `tocafix_team_category_version_id`)
                REFERENCES `tocafix_team_category` (`id`, `version_id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.team_category.language_id` FOREIGN KEY (`language_id`)
                REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
