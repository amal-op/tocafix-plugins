<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeactivateVarientWithoutPriceService
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
        $criteria->addAssociation("prices");
        $criteria->addSorting(new FieldSorting('id'));  

        $iterator = new RepositoryIterator($this->productRepository, $this->context, $criteria);

        $progressBar = new ProgressBar($output, $iterator->getTotal());
        $progressBar->start();

        while (($result = $iterator->fetch()) !== null) {
            $productUpdatedConfig = [];
            $products = $result->getEntities();

            /** @var ProductEntity $product */
            foreach ($products as $product) {
                $progressBar->advance();
                if ($product->getParentId()) {
                    if ($product->getPrice() && $product->getPrice()->first()->getNet() > 0) {
                        continue;
                    }

                    if ($product->getPrices()) {
                        /** @var ProductPriceEntity $price */
                        foreach ($product->getPrices() as $price) {
                            if ($price->getPrice()->first()->getNet() > 0) {
                                continue 2;
                            }
                        }
                    }

                    $productUpdatedConfig[] = [
                        'id' => $product->getId(),
                        'active' => false
                    ];
                }
            }

            if (!empty($productUpdatedConfig)) {
                $this->productRepository->update($productUpdatedConfig, $this->context);
            }
        }

        $progressBar->finish();

        $output->writeln('<info>Product Varient Config Updated successful</info>');
    }
}