<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class UpdateProductPriceTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'tocafix.product_price_update_task';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // 1 day
    }
}