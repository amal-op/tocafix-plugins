<?php

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\CustomerMailService;

class RecoveryPasswordEmailCommand extends Command
{
    protected static $defaultName = 'tocafix:send-mail:password';

    /**
     * @var CustomerMailService
     */
    public $sendCustomerMail;

    public function __construct(
        CustomerMailService $sendCustomerMail
    )
    {
        parent::__construct();
        $this->sendCustomerMail = $sendCustomerMail;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Send password recovery mail from interface')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will Send password recovery mail from the interface');
    }    // Actual code executed in the command

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->sendCustomerMail->executeCli($input, $output);
        // Exit code 0 for success
        return 0;
    }
}

