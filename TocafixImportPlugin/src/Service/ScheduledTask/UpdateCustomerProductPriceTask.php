<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class UpdateCustomerProductPriceTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'tocafix.customer_product_price_update_task';
    }

    public static function getDefaultInterval(): int
    {
        return 259200; // 3 days
    }
}