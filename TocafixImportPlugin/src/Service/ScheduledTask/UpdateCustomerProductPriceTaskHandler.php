<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service\ScheduledTask;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use TocafixImportPlugin\Service\UpdateCustomerProductPriceService;

class UpdateCustomerProductPriceTaskHandler extends ScheduledTaskHandler
{
    /**
     * @var UpdateCustomerProductPriceService
     */
    public $updateCustomerProductPriceService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        UpdateCustomerProductPriceService $updateCustomerProductPriceService
    )
    {
        parent::__construct($scheduledTaskRepository);
        $this->updateCustomerProductPriceService = $updateCustomerProductPriceService;
    }
    
    public static function getHandledMessages(): iterable
    {
        return [ UpdateCustomerProductPriceTask::class ];
    }

    public function run(): void
    {
        $this->updateCustomerProductPriceService->executeTask();
    }
}