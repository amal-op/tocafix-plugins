<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\FieldConfig;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ProductSliderStruct;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class CmsBundleProductSliderCmsElementResolver extends AbstractCmsElementResolver
{
    private const PRODUCT_SLIDER_ENTITY_FALLBACK = 'product-slider-entity-fallback';
    private const STATIC_SEARCH_KEY = 'product-slider';

    public function getType(): string
    {
        return 'cmsbundle-product-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $collection = new CriteriaCollection();

        $products = $config->get('products');
        if ($products === null) {
            return null;
        }

        if ($products->isStatic() && $products->getValue()) {
            $criteria = new Criteria($products->getArrayValue());
            $criteria->addAssociation('media');
            $collection->add(self::STATIC_SEARCH_KEY . '_' . $slot->getUniqueIdentifier(), ProductDefinition::class, $criteria);
        }

        if ($products->isMapped() && $products->getValue() && $resolverContext instanceof EntityResolverContext) {
            $criteria = $this->collectByEntity($resolverContext, $products);
            if ($criteria !== null) {
                $collection->add(self::PRODUCT_SLIDER_ENTITY_FALLBACK . '_' . $slot->getUniqueIdentifier(), ProductDefinition::class, $criteria);
            }
        }


        return $collection->all() ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $slider = new ProductSliderStruct();
        $slot->setData($slider);

        $productConfig = $config->get('products');
        if ($productConfig === null) {
            return;
        }

        if ($productConfig->isStatic()) {
            $this->enrichFromSearch($slider, $result, self::STATIC_SEARCH_KEY . '_' . $slot->getUniqueIdentifier(), $resolverContext->getSalesChannelContext());
        }

        if ($productConfig->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $products = $this->resolveEntityValue($resolverContext->getEntity(), $productConfig->getStringValue());
            if ($products === null) {
                $this->enrichFromSearch($slider, $result, self::PRODUCT_SLIDER_ENTITY_FALLBACK . '_' . $slot->getUniqueIdentifier(), $resolverContext->getSalesChannelContext());
            } else {
                $slider->setProducts($products);
            }
        }
    }

    private function enrichFromSearch(ProductSliderStruct $slider, ElementDataCollection $result, string $searchKey, SalesChannelContext $saleschannelContext): void
    {
        $searchResult = $result->get($searchKey);
        if ($searchResult === null) {
            return;
        }

        /** @var ProductCollection|null $products */
        $products = $searchResult->getEntities();
        if ($products === null) {
            return;
        }

        $slider->setProducts($products);
    }


    private function collectByEntity(EntityResolverContext $resolverContext, FieldConfig $config): ?Criteria
    {
        $entityProducts = $this->resolveEntityValue($resolverContext->getEntity(), $config->getStringValue());
        if ($entityProducts) {
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
