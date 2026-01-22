<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;

class GetDimensionTextOptionIdsService
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityRepository
     */
    private $propertyGroupOptionRepository;

    /**
     * @var EntityRepository
     */
    private $productConfigurationRepository;

    /**
     * @var EntityRepository
     */
    private $productOptionRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;
    public function __construct(
        EntityRepository $propertyGroupOptionRepository,
        EntityRepository $productConfigurationRepository,
        EntityRepository $productOptionRepository,
        LoggerInterface $logger
    ) {
        $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
        $this->productConfigurationRepository = $productConfigurationRepository;
        $this->productOptionRepository = $productOptionRepository;
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
        $criteria = new Criteria();
        $criteria->addAssociation('productConfiguratorSettings');
        $criteria->addAssociation('productOptions');

        $criteria->addFilter(new EqualsAnyFilter('group.name', ['Dimension text', 'Beschreibung Variante']));
        $optionSearch = $this->propertyGroupOptionRepository->search(
            $criteria,
            $this->context
        )->getEntities();

        $progressBar = new ProgressBar($output, count($optionSearch));
        $progressBar->start();
        $productConfigurationIds = [];
        $productOptionIds = [];

        /** @var PropertyGroupOptionEntity $option */
        foreach ($optionSearch as $option) {
            $productConfigurationIds = array_merge(
                $productConfigurationIds,
                array_values($option->getProductConfiguratorSettings()->getIds())
            );

            $productIds = array_values($option->getProductOptions()->getIds());
            $productOptionIdSet = array_map(static function (string $id) use ($option){
                return ['productId' => $id, 'optionId' => $option->getId()];
            }, $productIds);

            $productOptionIds = array_merge(
                $productOptionIds,
                $productOptionIdSet
            );
            
            $progressBar->advance();
            
        }

        if (!empty($productConfigurationIds)) {
            $productConfigurationIds = array_map(static function (string $id) {
                return ['id' => $id];
            }, $productConfigurationIds);

            $this->productConfigurationRepository->delete($productConfigurationIds, $this->context);
        }

        if (!empty($productOptionIds)) {
            $this->productOptionRepository->delete($productOptionIds, $this->context);
        }

        $progressBar->finish();

        $output->writeln('<info>Product Varient Config Updated successful</info>');
    }
}