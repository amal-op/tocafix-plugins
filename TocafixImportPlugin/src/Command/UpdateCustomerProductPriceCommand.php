<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\UpdateCustomerProductPriceService;

class UpdateCustomerProductPriceCommand extends Command
{
    protected static $defaultName = 'tocafix:update:customer-price';

    /**
     * @var UpdateCustomerProductPriceService
     */
    public $updateCustomerProductPriceService;

    public function __construct(
        UpdateCustomerProductPriceService $updateCustomerProductPriceService
    )
    {
        parent::__construct();
        $this->updateCustomerProductPriceService = $updateCustomerProductPriceService;
    }

    protected function configure()
    {
        $this
            // the short description shown while running "php bin/console list"
            ->setDescription('Update customer specific product prices from interface')

            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp('This command will update the customer specific product prices from the interface');
    }    // Actual code executed in the command

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->updateCustomerProductPriceService->executeCli($input, $output);
        // Exit code 0 for success
        return 0;
    }
}