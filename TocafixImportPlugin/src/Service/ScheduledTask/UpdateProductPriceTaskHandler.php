<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service\ScheduledTask;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use TocafixImportPlugin\Service\UpdateProductPriceService;

class UpdateProductPriceTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var UpdateProductPriceService
     */
    public $updateProductPriceService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        UpdateProductPriceService $updateProductPriceService
    )
    {
        parent::__construct($scheduledTaskRepository);
        $this->updateProductPriceService = $updateProductPriceService;
    }
    
    public static function getHandledMessages(): iterable
    {
        return [ UpdateProductPriceTask::class ];
    }

    public function run(): void
    {
        $this->updateProductPriceService->executeTask();
    }
}