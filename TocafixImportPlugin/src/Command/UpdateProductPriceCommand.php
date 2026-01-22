<?php declare(strict_types=1);

namespace TocafixImportPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TocafixImportPlugin\Service\UpdateProductPriceService;

class UpdateProductPriceCommand extends Command
{
    protected static $defaultName = 'tocafix:update:price';

    /**
     * @var UpdateProductPriceService
     */
    public $updateProductPriceService;

    public function __construct(
        UpdateProductPriceService $updateProductPriceService
    )
    {
        parent::__construct();
        $this->updateProductPriceService = $updateProductPriceService;
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
        $this->updateProductPriceService->executeCli($input, $output);
        // Exit code 0 for success
        return 0;
    }
}