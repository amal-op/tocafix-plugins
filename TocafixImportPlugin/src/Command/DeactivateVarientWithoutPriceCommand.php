<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\DeactivateVarientWithoutPriceService;

class DeactivateVarientWithoutPriceCommand extends Command
{
    protected static $defaultName = 'tocafix:deactivate:varient';

    /**
     * @var DeactivateVarientWithoutPriceService
     */
    public $deactivateVarientWithoutPrice;

    public function __construct(
        DeactivateVarientWithoutPriceService $deactivateVarientWithoutPrice
    )
    {
        parent::__construct();
        $this->deactivateVarientWithoutPrice = $deactivateVarientWithoutPrice;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('deactivate from interface')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will import the products from the interface');
    }    // Actual code executed in the command

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->deactivateVarientWithoutPrice->executeCli($input, $output);
        // Exit code 0 for success
        return 0;
    }
}

