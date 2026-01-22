<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\UpdateVarientConfigService;

class UpdateVarientConfigCommand extends Command
{
    protected static $defaultName = 'tocafix:update:varient-config';

    /**
     * @var UpdateVarientConfigService
     */
    public $updateVarientConfig;

    public function __construct(
        UpdateVarientConfigService $updateVarientConfig
    )
    {
        parent::__construct();
        $this->updateVarientConfig = $updateVarientConfig;
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
        $this->updateVarientConfig->executeCli($input, $output);
        // Exit code 0 for success
        return 0;
    }
}

