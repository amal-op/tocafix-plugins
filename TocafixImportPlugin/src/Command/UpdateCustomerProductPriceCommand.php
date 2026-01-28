<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\UpdateCustomerProductPriceService;

#[AsCommand(
    name: 'tocafix:update:customer-price',
    description: 'Update customer specific product prices from interface'
)]
class UpdateCustomerProductPriceCommand extends Command
{
    public function __construct(
        private readonly UpdateCustomerProductPriceService $updateCustomerProductPriceService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will update the customer specific product prices from the interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateCustomerProductPriceService->executeCli($input, $output);

        return Command::SUCCESS;
    }
}