<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\ImportProductsService;

#[AsCommand(
    name: 'tocafix:import:products',
    description: 'Import products from interface'
)]
class ImportProductsCommand extends Command
{
 
    public function __construct(
        private readonly ImportProductsService $importProductsService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will import the products from the interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importProductsService->executeCli($input, $output);

        return Command::SUCCESS;
    }
}