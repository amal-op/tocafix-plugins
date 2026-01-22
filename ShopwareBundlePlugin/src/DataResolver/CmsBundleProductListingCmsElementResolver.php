<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\SalesChannel\Struct\ProductListingStruct;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\SalesChannel\Listing\AbstractProductListingRoute;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter\PropertyListingFilterHandler;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingFeaturesSubscriber;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;

class CmsBundleProductListingCmsElementResolver extends AbstractCmsElementResolver
{
    private const STATIC_SEARCH_KEY_MANUAL = 'product-list-manual';

    private readonly AbstractProductListingRoute $listingRoute;

    /**
     * @internal
     */
    public function __construct(AbstractProductListingRoute $listingRoute)
    {
        $this->listingRoute = $listingRoute;
    }

    public function getType(): string
    {
        return 'cmsbundle-product-list';
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
            $criteria->addAssociation('cover');
            $collection->add(self::STATIC_SEARCH_KEY_MANUAL . '_' . $slot->getUniqueIdentifier(), ProductDefinition::class, $criteria);
        }


        return $collection->all() ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = new ProductListingStruct();
        $slot->setData($data);

        $request = $resolverContext->getRequest();
        $context = $resolverContext->getSalesChannelContext();

        $config = $slot->getFieldConfig();
        $productConfig = $config->get('products');

        if ($productConfig->isStatic()) {
            $searchResult = $result->get(self::STATIC_SEARCH_KEY_MANUAL . '_' . $slot->getUniqueIdentifier());
            if ($searchResult === null) {
                return;
            }

            $products = $searchResult->getEntities();
            if ($products === null) {
                return;
            }

            $result = new ProductListingResult(ProductDefinition::ENTITY_NAME, $searchResult->getTotal(), $searchResult->getEntities(), null, new Criteria(), $context->getContext());

            $data->setListing($result);
        } else {

            $this->restrictFilters($slot, $request);

            if ($this->isCustomSorting($slot)) {
                $this->restrictSortings($request, $slot);
                $this->addDefaultSorting($request, $slot);
            }

            $navigationId = $this->getNavigationId($request, $context);

            $criteria = new Criteria();
            $criteria->setTitle('cms::product-listing');

            $listing = $this->listingRoute
                ->load($navigationId, $request, $context, $criteria)
                ->getResult();

            $data->setListing($listing);
        }
    }

    private function getNavigationId(Request $request, SalesChannelContext $salesChannelContext): string
    {
        if ($navigationId = $request->get('navigationId')) {
            return $navigationId;
        }

        $params = $request->attributes->get('_route_params');

        if ($params && isset($params['navigationId'])) {
            return $params['navigationId'];
        }

        return $salesChannelContext->getSalesChannel()->getNavigationCategoryId();
    }

    private function isCustomSorting(CmsSlotEntity $slot): bool
    {
        $config = $slot->getTranslation('config');

        if ($config && isset($config['useCustomSorting']) && isset($config['useCustomSorting']['value'])) {
            return $config['useCustomSorting']['value'];
        }

        return false;
    }

    private function addDefaultSorting(Request $request, CmsSlotEntity $slot): void
    {
        if ($request->get('order')) {
            return;
        }

        $config = $slot->getTranslation('config');

        if ($config && isset($config['defaultSorting']) && isset($config['defaultSorting']['value']) && $config['defaultSorting']['value']) {
            $request->request->set('order', $config['defaultSorting']['value']);

            return;
        }

        // if we have no specific order given at this point, set the order to be the highest's priority available sorting
        if ($request->get('availableSortings')) {
            $availableSortings = $request->get('availableSortings');
            arsort($availableSortings, \SORT_DESC | \SORT_NUMERIC);

            $request->request->set('order', array_key_first($availableSortings));
        }
    }

    private function restrictSortings(Request $request, CmsSlotEntity $slot): void
    {
        $config = $slot->getTranslation('config');

        if (!$config || !isset($config['availableSortings']) || !isset($config['availableSortings']['value'])) {
            return;
        }

        $request->request->set('availableSortings', $config['availableSortings']['value']);
    }

    private function restrictFilters(CmsSlotEntity $slot, Request $request): void
    {
        // setup the default behavior
        $defaults = ['manufacturer-filter', 'rating-filter', 'shipping-free-filter', 'price-filter', 'property-filter'];

        $request->request->set(PropertyListingFilterHandler::PROPERTY_GROUP_IDS_REQUEST_PARAM, null);

        $config = $slot->get('config');

        if (isset($config['propertyWhitelist']['value']) && (is_countable($config['propertyWhitelist']['value']) ? \count($config['propertyWhitelist']['value']) : 0) > 0) {
            $request->request->set(PropertyListingFilterHandler::PROPERTY_GROUP_IDS_REQUEST_PARAM, $config['propertyWhitelist']['value']);
        }

        if (!isset($config['filters']['value'])) {
            return;
        }

        // apply config settings
        $config = explode(',', (string) $config['filters']['value']);

        foreach ($defaults as $filter) {
            if (\in_array($filter, $config, true)) {
                continue;
            }

            $request->request->set($filter, false);
        }
    }
}
