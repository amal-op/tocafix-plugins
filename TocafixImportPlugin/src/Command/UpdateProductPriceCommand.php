<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

// 1. Add this import
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\UpdateProductPriceService;

#[AsCommand(
    name: 'tocafix:update:price',
    description: 'Update product prices from interface'
)]
class UpdateProductPriceCommand extends Command
{
    
    public function __construct(
        private readonly UpdateProductPriceService $updateProductPriceService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will update the product prices from the interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateProductPriceService->executeCli($input, $output);

        return Command::SUCCESS;
    }
}