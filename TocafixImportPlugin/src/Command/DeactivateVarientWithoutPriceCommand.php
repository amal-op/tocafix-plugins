<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\DeactivateVarientWithoutPriceService;

#[AsCommand(
    name: 'tocafix:deactivate:varient',
    description: 'Deactivate variants without price from interface'
)]
class DeactivateVarientWithoutPriceCommand extends Command
{
    public function __construct(
        private readonly DeactivateVarientWithoutPriceService $deactivateVarientWithoutPrice
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will deactivate variants that do not have a price');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deactivateVarientWithoutPrice->executeCli($input, $output);
        
        return Command::SUCCESS;
    }
}