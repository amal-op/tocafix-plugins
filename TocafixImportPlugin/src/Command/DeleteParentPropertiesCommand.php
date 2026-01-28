<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\DeleteParentPropertiesService;

#[AsCommand(
    name: 'tocafix:delete:not-varient-properties',
    description: 'Delete properties from parent products that are not variant properties'
)]
class DeleteParentPropertiesCommand extends Command
{
    public function __construct(
        private readonly DeleteParentPropertiesService $deleteParentPropertiesService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will delete properties from parent products that should only exist on variants');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deleteParentPropertiesService->executeCli($input, $output);
        
        return Command::SUCCESS;
    }
}