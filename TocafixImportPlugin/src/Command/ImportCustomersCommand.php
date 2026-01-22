<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\ImportCustomersService;

class ImportCustomersCommand extends Command
{
    protected static $defaultName = 'tocafix:import:customers';

    /**
     * @var ImportCustomersService
     */
    public $importCustomersService;

    public function __construct(
        ImportCustomersService $importCustomersService
    )
    {
        parent::__construct();
        $this->importCustomersService = $importCustomersService;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Import customers from interface')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will import the customers from the interface');
    }    // Actual code executed in the command

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->importCustomersService->executeCli($input, $output);
        // Exit code 0 for success
        return 0;
    }
}

