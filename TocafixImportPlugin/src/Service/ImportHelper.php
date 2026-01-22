<?php

declare(strict_types=1);

namespace TocafixImportPlugin\Service;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Property\Aggregate\PropertyGroupOption\PropertyGroupOptionEntity;
use Shopware\Core\Content\Property\PropertyGroupEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Uuid\Uuid;

class ImportHelper
{
    /**
     * @var EntityRepository
     */
    protected $productRepository;

    /**
     * @var EntityRepository
     */
    protected $productVisibilityRepository;

    /**
     * @var EntityRepository
     */
    protected $productConfiguratorSettingRepository;

    /**
     * @var EntityRepository
     */
    protected $propertyGroupRepository;

    /**
     * @var EntityRepository
     */
    protected $propertyGroupOptionRepository;

    public function __construct(
        EntityRepository $productRepository,
        EntityRepository $productVisibilityRepository,
        EntityRepository $productConfiguratorSettingRepository,
        EntityRepository $propertyGroupRepository,
        EntityRepository $propertyGroupOptionRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productVisibilityRepository = $productVisibilityRepository;
        $this->productConfiguratorSettingRepository = $productConfiguratorSettingRepository;
        $this->propertyGroupRepository = $propertyGroupRepository;
        $this->propertyGroupOptionRepository = $propertyGroupOptionRepository;
    }

    public function getProductByProductNumber(string $productNumber, Context $context): ?ProductEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productNumber', $productNumber));
        $criteria->addAssociation('media');
        $criteria->addAssociation('cover');
        $productSearch = $this->productRepository->search(
            $criteria,
            $context
        );

        return $productSearch->getEntities()->first();
    }

    public function getProductVisibilityId(string $productId, string $salesChannelId, Context $context): string
    {
        $productVisibilitySearchCriteria = new Criteria();
        $productVisibilitySearchCriteria->addFilter(new EqualsFilter('productId', $productId));
        $productVisibilitySearchCriteria->addFilter(new EqualsFilter('salesChannelId', $salesChannelId));
        $productVisibilitySearch = $this->productVisibilityRepository->search($productVisibilitySearchCriteria, $context);
        /** @var ProductVisibilityEntity */
        $productVisibility = $productVisibilitySearch->getEntities()->first();

        return $productVisibility ? $productVisibility->getId() : Uuid::randomHex();
    }

    public function getConfiguratorSettingId(string $productId, string $optionId, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('productId', $productId));
        $criteria->addFilter(new EqualsFilter('optionId', $optionId));

        return $this->productConfiguratorSettingRepository->searchIds($criteria, $context)->firstId();
    }

    public function getExistingPropertyGroup(string $propertyEnglishName, Context $context): ?PropertyGroupEntity
    {
        $propertySearchCriteria = new Criteria();
        $propertySearchCriteria
            ->addFilter(new EqualsFilter('translations.name', $propertyEnglishName))
            ->addFilter(new EqualsFilter('name', $propertyEnglishName));
        $propertySearchCriteria->addSorting(new FieldSorting('createdAt', FieldSorting::DESCENDING));
        
        $propertySearch = $this->propertyGroupRepository->search($propertySearchCriteria, $context);
        
        return $propertySearch->getEntities()->first();
    }

    public function getPropertyOptionId(string $existingPropertyGroupId, string $propertyValue, Context $context): ?string
    {
        $propertyOptionSearchCriteria = new Criteria();
        $propertyOptionSearchCriteria->addFilter(new EqualsFilter('groupId', $existingPropertyGroupId));
        $propertyOptionSearchCriteria->addFilter(new EqualsFilter('translations.name', $propertyValue));
        $propertyOptionSearch = $this->propertyGroupOptionRepository->search($propertyOptionSearchCriteria, $context);
        
        /** @var PropertyGroupOptionEntity */
        $propertyOption = $propertyOptionSearch->getEntities()->first();

        return $propertyOption ? $propertyOption->getId() : Uuid::randomHex();
    }
}