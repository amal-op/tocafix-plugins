<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use ShopwareBundlePlugin\Struct\CategoryStruct;

class CmsBundleCategorySliderCmsElementResolver extends AbstractCmsElementResolver
{
    private const PRODUCT_SLIDER_ENTITY_FALLBACK = 'category-slider-entity-fallback';
    private const STATIC_SEARCH_KEY = 'category-slider';

    public function getType(): string
    {
        return 'cmsbundle-category-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $collection = new CriteriaCollection();

        $categories = $config->get('categories');
        if ($categories === null) {
            return null;
        }

        if ($categories->isStatic() && $categories->getValue()) {
            $criteria = new Criteria($categories->getArrayValue());
            $criteria->addAssociation('media');
            $collection->add(self::STATIC_SEARCH_KEY . '_' . $slot->getUniqueIdentifier(), CategoryDefinition::class, $criteria);
        }

        if ($categories->isMapped() && $categories->getValue() && $resolverContext instanceof EntityResolverContext) {
            $criteria = $this->collectByEntity($resolverContext, $categories);
            if ($criteria !== null) {
                $collection->add(self::PRODUCT_SLIDER_ENTITY_FALLBACK . '_' . $slot->getUniqueIdentifier(), CategoryDefinition::class, $criteria);
            }
        }


        return $collection->all() ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $slider = new CategoryStruct();
        $slot->setData($slider);

        $categoryConfig = $config->get('categories');
        if ($categoryConfig === null) {
            return;
        }

        if ($categoryConfig->isStatic()) {
            $this->enrichFromSearch($slider, $result, self::STATIC_SEARCH_KEY . '_' . $slot->getUniqueIdentifier(), $resolverContext->getSalesChannelContext());
        }

        if ($categoryConfig->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $categories = $this->resolveEntityValue($resolverContext->getEntity(), $categoryConfig->getStringValue());
            if ($categories === null) {
                $this->enrichFromSearch($slider, $result, self::PRODUCT_SLIDER_ENTITY_FALLBACK . '_' . $slot->getUniqueIdentifier(), $resolverContext->getSalesChannelContext());
            } else {
                $slider->setCategories($categories);
            }
        }
    }

    private function enrichFromSearch(CategoryStruct $slider, ElementDataCollection $result, string $searchKey, SalesChannelContext $saleschannelContext): void
    {
        $searchResult = $result->get($searchKey);
        if ($searchResult === null) {
            return;
        }

        /** @var CategoryCollection|null $categories */
        $categories = $searchResult->getEntities();
        if ($categories === null) {
            return;
        }

        $slider->setCategories($categories);
    }


    private function collectByEntity(EntityResolverContext $resolverContext, FieldConfig $config): ?Criteria
    {
        $entityCategories = $this->resolveEntityValue($resolverContext->getEntity(), $config->getStringValue());
        if ($entityCategories) {
            return null;
        }

        $criteria = $this->resolveCriteriaForLazyLoadedRelations($resolverContext, $config);
        if ($criteria === null) {
            return null;
        }

        $criteria->addAssociation('media');

        return $criteria;
    }
}
