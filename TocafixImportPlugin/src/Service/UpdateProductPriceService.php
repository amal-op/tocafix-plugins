<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Content\Product\Aggregate\ProductPrice\ProductPriceEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Indexing\EntityIndexerRegistry;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\OrFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateProductPriceService
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityRepository
     */
    private $productRepository;

    /**
     * @var EntityRepository
     */
    private $productPriceRepository;

    /**
     * @var EntityRepository
     */
    private $salesChannelRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityRepository $productRepository,
        EntityRepository $salesChannelRepository,
        EntityRepository $productPriceRepository,
        LoggerInterface $logger
    ) {
        $this->productRepository = $productRepository;
        $this->salesChannelRepository = $salesChannelRepository;
        $this->productPriceRepository = $productPriceRepository;
        $this->logger = $logger;

        $this->context = Context::createDefaultContext();
        $this->context->addState(EntityIndexerRegistry::USE_INDEXING_QUEUE);
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
        $productSalesChannel = $this->getSalesChannelByName("tocafix.ch");

        $criteria = new Criteria();
        $criteria->setLimit(100);
        $criteria->addAssociation('prices');
        $criteria->addSorting(new FieldSorting('id'));

        $iterator = new RepositoryIterator($this->productRepository, $this->context, $criteria);

        $progressBar = new ProgressBar($output, $iterator->getTotal());
        $progressBar->start();

        while (($result = $iterator->fetch()) !== null) {
            $productUpdatedPrice = [];
            $productUpdatedAdvancedPrice = [];
            $products = $result->getEntities();
            
            /** @var ProductEntity $product */
            foreach ($products as $product) {
                $progressBar->advance();
                // if (!isset($product->getCustomFields()['previous_price_latest']) || !$product->getCustomFields()['previous_price_latest']) {
                    // if(($product->getUpdatedAt()->format("Y-m-d H:i:s") < "2024-06-19 08:50:27") || $product->getUpdatedAt()->format("Y-m-d H:i:s") === null) {
                        $price = $this->getCalculatedProductPrices($product, $productSalesChannel);
                        if ($price) {
                            $productUpdatedPrice[] = [
                                'id' => $product->getId(),
                                'price' => $price,
                                'customFields' => ['previous_price_latest' => $product->getPrice()]
                            ];
                        }
                    // }
                // }
                $basicUnit = $product->getReferenceUnit();
                if ($basicUnit) {
                    $prices = $product->getPrices();

                    /** @var ProductPriceEntity $price */
                    foreach ($prices as $price) {
                        // if ($price && (!isset($price->getCustomFields()['previous_price_advanced']) || !$price->getCustomFields()['previous_price_advanced'])) {
                        // if ($price) {
                            $advancedPrice = $this->getCalculatedAdvancedProductPrices($product, $price, $productSalesChannel);
                            if ($advancedPrice) {
                                $productUpdatedAdvancedPrice[] = [
                                    'id' => $price->getId(),
                                    'price' => $advancedPrice,
                                    'customFields' => ['previous_price_advanced' => $price->getPrice()]
                                ];
                            }

                        // }
                    }
                }
            }

            if (!empty($productUpdatedAdvancedPrice)) {
                $this->productPriceRepository->update($productUpdatedAdvancedPrice, $this->context);
            }

            if (!empty($productUpdatedPrice)) {
                $this->productRepository->update($productUpdatedPrice, $this->context);
            }
        }

        $progressBar->finish();

        $output->writeln('<info>Product Price Update successful</info>');
    }

    /**
     * Executes the CLI command.
     *
     * @param InputInterface $input The input interface.
     * @param OutputInterface $output The output interface.
     * @return void
     */
    public function executeTask()
    {
        $productSalesChannel = $this->getSalesChannelByName("tocafix.ch");
        $yesterday = new \DateTime('now -1 day');

        $criteria = new Criteria();
        $criteria->setLimit(100);
        $criteria->addAssociation('prices');
        $criteria->addFilter(new OrFilter([
            new RangeFilter('updatedAt', ['gte' => $yesterday->format(Defaults::STORAGE_DATE_TIME_FORMAT)]),
            new EqualsFilter('updatedAt', null),
            new RangeFilter('prices.updatedAt', ['gte' => $yesterday->format(Defaults::STORAGE_DATE_TIME_FORMAT)]),
            new EqualsFilter('prices.updatedAt', null),
        ]));
        $criteria->addSorting(new FieldSorting('id'));

        $iterator = new RepositoryIterator($this->productRepository, $this->context, $criteria);

        while (($result = $iterator->fetch()) !== null) {
            $productUpdatedPrice = [];
            $productUpdatedAdvancedPrice = [];
            $products = $result->getEntities();
            
            /** @var ProductEntity $product */
            foreach ($products as $product) {
                if((!isset($product->getCustomFields()['price_updated_at']) || $product->getCustomFields()['price_updated_at'] <= $yesterday->format(Defaults::STORAGE_DATE_TIME_FORMAT))) {
                    $price = $this->getCalculatedProductPrices($product, $productSalesChannel);
                    if ($price) {
                        $productUpdatedPrice[] = [
                            'id' => $product->getId(),
                            'price' => $price,
                            'customFields' => ['previous_price_latest' => $product->getPrice(), 'price_updated_at' => (new \DateTime('now'))->format(Defaults::STORAGE_DATE_TIME_FORMAT)]
                        ];
                    }
                }
                
                $basicUnit = $product->getReferenceUnit();
                if ($basicUnit) {
                    $prices = $product->getPrices();

                    /** @var ProductPriceEntity $price */
                    foreach ($prices as $price) {
                        if ($price && (!isset($price->getCustomFields()['price_updated_at']) || $price->getCustomFields()['price_updated_at'] <= $yesterday->format(Defaults::STORAGE_DATE_TIME_FORMAT))) {
                            $advancedPrice = $this->getCalculatedAdvancedProductPrices($product, $price, $productSalesChannel);
                            if ($advancedPrice) {
                                $productUpdatedAdvancedPrice[] = [
                                    'id' => $price->getId(),
                                    'price' => $advancedPrice,
                                    'customFields' => ['previous_price_advanced' => $price->getPrice(), 'price_updated_at' => (new \DateTime('now'))->format(Defaults::STORAGE_DATE_TIME_FORMAT)]
                                ];
                            }
                        }
                    }
                }
            }

            if (!empty($productUpdatedAdvancedPrice)) {
                $this->productPriceRepository->update($productUpdatedAdvancedPrice, $this->context);
            }

            if (!empty($productUpdatedPrice)) {
                $this->productRepository->update($productUpdatedPrice, $this->context);
            }
        }

        $this->logger->info('Product Prices Updated successful');
    }

    /**
     * Calculates the product prices based on the given product and sales channel.
     *
     * @param ProductEntity $product The product entity.
     * @param SalesChannelEntity $productSalesChannel The sales channel entity.
     * @return array|null The calculated product prices or null if no prices are found.
     */
    private function getCalculatedProductPrices(ProductEntity $product, SalesChannelEntity $productSalesChannel)
    {  
        $price = null;
        foreach ($productSalesChannel->getCurrencies() as $currency)
        {
            $netPrice = $product->getPrice()?->getCurrencyPrice($currency->getId())?->getNet() ?? 0;
            if ($netPrice > 0) {
                $basicUnit = $product->getReferenceUnit();
                if ($basicUnit) {
                    $netPrice = $netPrice / $basicUnit;
                    $vat = $product->getTax() ? $netPrice * $product->getTax()->getTaxRate() / 100 : 0;
                    $price[] = [
                        'currencyId' => $currency->getId(),
                        'net' => $netPrice,
                        'gross' => $netPrice + $vat,
                        'linked' => true
                    ];
                }
            }
        }

        return $price;
    }

    /**
     * Calculates the product prices based on the given product and sales channel.
     *
     * @param ProductEntity $product The product entity.
     * @param SalesChannelEntity $productSalesChannel The sales channel entity.
     * @return array|null The calculated product prices or null if no prices are found.
     */
    private function getCalculatedAdvancedProductPrices(ProductEntity $product, ProductPriceEntity $advancedPrice, SalesChannelEntity $productSalesChannel)
    {  
        $price = null;
        foreach ($productSalesChannel->getCurrencies() as $currency)
        {
            $netPrice = $advancedPrice->getPrice()->getCurrencyPrice($currency->getId())->getNet();
            if ($netPrice > 0) {
                $basicUnit = $product->getReferenceUnit();
                if ($basicUnit) {
                    $netPrice = $netPrice / $basicUnit;
                    $vat = $product->getTax() ? $netPrice * $product->getTax()->getTaxRate() / 100 : 0;
                    $price[] = [
                        'currencyId' => $currency->getId(),
                        'net' => $netPrice,
                        'gross' => $netPrice + $vat,
                        'linked' => true
                    ];
                }
            }
        }

        return $price;
    }

    /**
     * Retrieves a sales channel entity by its name.
     *
     * @param string $name The name of the sales channel.
     * @return SalesChannelEntity The sales channel entity.
     */
    private function getSalesChannelByName(string $name)
    {
        $salesChannelCriteria = new Criteria();
        $salesChannelCriteria->addFilter(new EqualsFilter('translations.name', $name));
        $salesChannelCriteria->addAssociation('currencies');

        $salesChannelSearch = $this->salesChannelRepository->search(
            $salesChannelCriteria,
            $this->context
        );

        /** @var SalesChannelEntity */
        return $salesChannelSearch->getEntities()->first();
    }
}