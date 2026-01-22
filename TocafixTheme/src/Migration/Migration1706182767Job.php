<?php declare(strict_types=1);

namespace TocafixTheme\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1706182767Job extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1706182767;
    }

    public function update(Connection $connection): void
    {
        $connection->executeUpdate('
CREATE TABLE IF NOT EXISTS `tocafix_job` (
    `id`                            BINARY(16)                              NOT NULL,
    `job_date`                      DATE                                    NOT NULL,
    `media_id`                      BINARY(16)                              NULL,
    `active`                        TINYINT(1)   DEFAULT 1                  NOT NULL,
    `created_at`                    DATETIME(3)                             NOT NULL,
    `updated_at`                    DATETIME(3)                             NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.tocafix_job.media_id` FOREIGN KEY (`media_id`)
        REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
