<?php declare(strict_types=1);

namespace TocafixTheme\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1706182772JobTranslation extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1706182772;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `tocafix_job_translation` (
              `tocafix_job_id` BINARY(16) NOT NULL,
              `language_id` BINARY(16) NOT NULL,
              `name` VARCHAR(255),
              `short_description_teaser` LONGTEXT NULL,
              `short_description_detail` LONGTEXT NULL,
              `description` LONGTEXT NULL,
              `created_at` DATETIME(3) NOT NULL,
              `updated_at` DATETIME(3) NULL,
              PRIMARY KEY (`tocafix_job_id`, `language_id`),
              CONSTRAINT `fk.tocafix_job_translation.job_id` FOREIGN KEY (`tocafix_job_id`)
                REFERENCES `tocafix_job` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
              CONSTRAINT `fk.tocafix_job_translation.language_id` FOREIGN KEY (`language_id`)
                REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
