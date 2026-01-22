<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\DeleteParentPropertiesService;

class DeleteParentPropertiesCommand extends Command
{
    protected static $defaultName = 'tocafix:delete:not-varient-properties';

    /**
     * @var DeleteParentPropertiesService
     */
    public $deleteParentPropertiesService;

    public function __construct(
        DeleteParentPropertiesService $deleteParentPropertiesService
    )
    {
        parent::__construct();
        $this->deleteParentPropertiesService = $deleteParentPropertiesService;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Import products from interface')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will import the products from the interface');
    }    // Actual code executed in the command

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deleteParentPropertiesService->executeCli($input, $output);
        // Exit code 0 for success
        return 0;
    }
}

