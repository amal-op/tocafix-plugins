<?php
declare(strict_types=1);

namespace VioCustomerPrice\Commands;

use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Context;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;
use VioCustomerPrice\Services\ImportService;

#[AsCommand('vio:customer:prices:import')]
class VioCustomerPriceImportCommand extends Command
{
    protected ImportService $importService;

    public function setImportService(ImportService $importService): void
    {
        $this->importService = $importService;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::REQUIRED, 'File to be imported')
            ->addOption('delete', 'd', InputOption::VALUE_NONE, 'delete imported file');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fileName = $input->getArgument('file');
        if (empty($fileName)) {
            throw new \InvalidArgumentException('filename is empty!');
        }
        if (!is_file($fileName)) {
            throw new \InvalidArgumentException("$fileName isn't a file!");
        }
        if (!is_readable($fileName)) {
            throw new \InvalidArgumentException("$fileName isn't a readable file!");
        }
        $file = new File($fileName);

        $output->writeln(\sprintf('start importing from file "%d"...', $fileName));
        $context = new Context(new SystemSource());
        $count = $this->importService->import($file, $context);

        $output->writeln(\sprintf('%d prices imported', $count));

        if ($input->getOption('delete')) {
            if (unlink($fileName)) {
                $output->writeln('deleted imported file');
            } else {
                $output->writeln('couldn\'t deleted imported file');

                return Command::FAILURE;
            }
        }

        $output->writeln('');

        return Command::SUCCESS;
    }
}
