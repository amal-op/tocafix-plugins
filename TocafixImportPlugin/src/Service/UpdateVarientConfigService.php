<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateVarientConfigService
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityRepository
     */
    private $productRepository;

    public function __construct(
        EntityRepository $productRepository
    ) {
        $this->productRepository = $productRepository;

        $this->context = Context::createDefaultContext();
    }

    /**
     * Executes the CLI command.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     * @return void
     */
    public function executeCli(InputInterface $input, OutputInterface $output)
    {
        $criteria = new Criteria();
        $criteria->setLimit(500);
        $criteria->addFilter(new EqualsFilter('parentId', null));
        $criteria->addSorting(new FieldSorting('id'));  

        $iterator = new RepositoryIterator($this->productRepository, $this->context, $criteria);

        $progressBar = new ProgressBar($output, $iterator->getTotal());
        $progressBar->start();

        while (($result = $iterator->fetch()) !== null) {
            $productUpdatedConfig = [];
            $products = $result->getEntities();

            foreach ($products as $product) {
                $progressBar->advance();
                $productUpdatedConfig[] = [
                    'id' => $product->getId(),
                    'variantListingConfig' => [
                        'displayParent' => true
                    ]
                ];
            }

            if (!empty($productUpdatedConfig)) {
                $this->productRepository->update($productUpdatedConfig, $this->context);
            }
          
        }

        $progressBar->finish();

        $output->writeln('<info>Product Varient Config Updated successful</info>');
    }
}