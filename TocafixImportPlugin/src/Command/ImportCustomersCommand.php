<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\ImportCustomersService;

#[AsCommand(
    name: 'tocafix:import:customers',
    description: 'Import customers from interface'
)]
class ImportCustomersCommand extends Command
{
    public function __construct(
        private readonly ImportCustomersService $importCustomersService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will import the customers from the interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importCustomersService->executeCli($input, $output);

        return Command::SUCCESS;
    }
}