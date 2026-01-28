<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\GetDimensionTextOptionIdsService;

#[AsCommand(
    name: 'tocafix:get:dimension-text-option-ids',
    description: 'Import products from interface'
)]
class GetDimensionTextOptionIdsCommand extends Command
{
    // In Shopware 6.6 (PHP 8.2+), use constructor promotion for cleaner code
    public function __construct(
        private readonly GetDimensionTextOptionIdsService $getDimensionTextOptionIdsService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will import the products from the interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->getDimensionTextOptionIdsService->executeCli($input, $output);

        return Command::SUCCESS; // Use the Command constant instead of 0
    }
}