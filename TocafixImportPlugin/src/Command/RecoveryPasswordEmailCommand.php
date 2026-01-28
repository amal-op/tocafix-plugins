<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\CustomerMailService;

#[AsCommand(
    name: 'tocafix:send-mail:password',
    description: 'Send password recovery mail from interface'
)]
class RecoveryPasswordEmailCommand extends Command
{

    public function __construct(
        private readonly CustomerMailService $sendCustomerMail
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setHelp('This command will Send password recovery mail from the interface');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sendCustomerMail->executeCli($input, $output);

        return Command::SUCCESS;
    }
}