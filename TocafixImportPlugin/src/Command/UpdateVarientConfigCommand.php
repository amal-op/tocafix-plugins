<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\UpdateVarientConfigService;

#[AsCommand(
    name: 'tocafix:update:varient-config',
    description: 'Update variant configuration from interface'
)]
class UpdateVarientConfigCommand extends Command
{
    /**
     * Updated to use Constructor Promotion (PHP 8.2)
     */
    public function __construct(
        private readonly UpdateVarientConfigService $updateVarientConfig
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will update the variant configuration from the interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateVarientConfig->executeCli($input, $output);

        return Command::SUCCESS;
    }
}