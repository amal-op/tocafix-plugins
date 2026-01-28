<?php declare(strict_types=1);

namespace TocafixTheme\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1706160561TocafixTeam extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1706160561;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
CREATE TABLE IF NOT EXISTS `tocafix_team` (
    `id`                            BINARY(16)                              NOT NULL,
    `email`                         VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `phone_number`                  VARCHAR(255) COLLATE utf8mb4_unicode_ci NULL,
    `media_id`                      BINARY(16)                              NULL,
    `sort_order`                    INTEGER                                 NOT NULL,
    `active`                        TINYINT(1)   DEFAULT 1                  NOT NULL,
    `created_at`                    DATETIME(3)                             NOT NULL,
    `updated_at`                    DATETIME(3)                             NULL,
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.tocafix_team.media_id` FOREIGN KEY (`media_id`)
        REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
