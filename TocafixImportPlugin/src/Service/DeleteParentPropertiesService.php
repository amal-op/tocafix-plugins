<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Shopware\Core\Content\Product\Aggregate\ProductConfiguratorSetting\ProductConfiguratorSettingEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\RepositoryIterator;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteParentPropertiesService
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityRepository
     */
    private $productConfiguratorSettingRepository;

    public function __construct(
        EntityRepository $productConfiguratorSettingRepository
    ) {
        $this->productConfiguratorSettingRepository = $productConfiguratorSettingRepository;

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
        $criteria->addAssociation('option.group');

        $iterator = new RepositoryIterator($this->productConfiguratorSettingRepository, $this->context, $criteria);

        $progressBar = new ProgressBar($output, $iterator->getTotal());
        $progressBar->start();
        $productConfiguratorUpdatedConfig = [];

        while (($result = $iterator->fetch()) !== null) {
            $productConfigurators = $result->getEntities();

            /** @var ProductConfiguratorSettingEntity $productConfigurator */
            foreach ($productConfigurators as $productConfigurator) {
                $progressBar->advance();
                if (!in_array ($productConfigurator->getOption()->getGroupId(), $productConfiguratorUpdatedConfig)) {
                    $productConfiguratorUpdatedConfig[] = $productConfigurator->getOption()->getGroupId();
                }
            }
        }

        $progressBar->finish();

        $output->writeln('<info>Product Varient Config Updated successful</info>');
    }
}