<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use VioCustomerPrice\Entity\CustomerPriceCollection;
use VioCustomerPrice\Entity\CustomerPriceEntity;

class UpdateCustomerProductPriceService
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityRepository
     */
    private $customerPricesRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        EntityRepository $customerPricesRepository,
        LoggerInterface $logger
    ) {
        $this->customerPricesRepository = $customerPricesRepository;
        $this->logger = $logger;

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
        error_reporting(-1);
        ini_set('memory_limit', '8192M');
        $criteria = new Criteria();
        $criteria->setLimit(1000);
        $criteria->addFilter(new EqualsFilter('customFields.previous_price', null));
        $criteria->addFilter(
            new NotFilter(
                NotFilter::CONNECTION_AND,
                [
                    new EqualsFilter('price', 0)
                ]
            )
        );
        $criteria->addAssociation('product.referenceUnit');
        $criteria->addSorting(new FieldSorting('id'));

        $iterator = new RepositoryIterator($this->customerPricesRepository, $this->context, $criteria);

        $progressBar = new ProgressBar($output, $iterator->getTotal());
        $progressBar->start();

        while (($result = $iterator->fetch()) !== null) {
            $this->processBatch($result->getEntities(), $progressBar);
            sleep(2);
        }

        $progressBar->finish();
        $output->writeln('<info>Product Customer Specific Prices Updated successful</info>');
    }

    /**
     * Executes the CLI command.
     *
     * @return void
     */
    public function executeTask()
    {
        $criteria = new Criteria();
        $criteria->setLimit(1000);
        $this->logger->info('Product Customer Specific Prices started');
        $criteria->addFilter(new EqualsFilter('customFields.previous_price', null));
        $criteria->addFilter(
            new NotFilter(
                NotFilter::CONNECTION_AND,
                [
                    new EqualsFilter('price', 0)
                ]
            )
        );
        $criteria->addAssociation('product.referenceUnit');
        $criteria->addSorting(new FieldSorting('id'));

        $iterator = new RepositoryIterator($this->customerPricesRepository, $this->context, $criteria);
        $this->logger->info("Total: ". $iterator->getTotal());
        $batchCount = 0;

        while (($result = $iterator->fetch()) !== null) {
            $this->processBatch($result->getEntities(), null);
            sleep(2);
            $batchCount++;
            $this->logger->info("Processed: ". $batchCount * 1000);
        }

        $this->logger->info('Product Customer Specific Prices Updated successful');
    }

    private function processBatch(CustomerPriceCollection $customerPrices, ?ProgressBar $progressBar): void
    {
        $customerSpecificProductPrice = [];
        
        /** @var CustomerPriceEntity $customerPrice */
        foreach ($customerPrices as $customerPrice) {
            if ($progressBar) {
                $progressBar->advance();
            }

            $basicUnit = $customerPrice->getProduct()->getReferenceUnit();
            if ($basicUnit && !isset($customerPrice->getCustomFields()['previous_price'])) {
                $price = $customerPrice->getPrice() / $basicUnit;
                if ($price > 0) {
                    $customerSpecificProductPrice[] = [
                        'id' => $customerPrice->getId(),
                        'price' => $price,
                        'customFields' => ['previous_price' => $customerPrice->getPrice()]
                    ];
                }

            }

            // Update in smaller batches to reduce memory usage
            if (count($customerSpecificProductPrice) >= 200) {
                $this->updateBatch($customerSpecificProductPrice);
                $customerSpecificProductPrice = [];
            }
        }

        // Update any remaining items
        if (!empty($customerSpecificProductPrice)) {
            $this->updateBatch($customerSpecificProductPrice);
        }
    }

    private function updateBatch(array $customerSpecificProductPrice)
    {
        $this->customerPricesRepository->update($customerSpecificProductPrice, $this->context);
    }
}