<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerDefinition;
use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Content\Product\SalesChannel\Listing\AbstractProductListingRoute;
use Shopware\Core\Content\Product\SalesChannel\Listing\Filter\PropertyListingFilterHandler;
use Shopware\Core\Content\Product\SalesChannel\Listing\Processor\CompositeListingProcessor;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use ShopwareBundlePlugin\Struct\ManufacturerStruct;
use Symfony\Component\HttpFoundation\Request;

class CmsBundleManufacturerDetailCmsElementResolver extends AbstractCmsElementResolver
{
    private const STATIC_MANUFACTURER_KEY_MANUAL = 'manufacturer';

    /** 
     * @var CompositeListingProcessor 
     */
    private $processor;

    /**
     * @var AbstractProductListingRoute
     */
    private $listingRoute;

    public function __construct(?CompositeListingProcessor $processor, AbstractProductListingRoute $listingRoute)
    {
        $this->processor = $processor;
        $this->listingRoute = $listingRoute;
    }

    public function getType(): string
    {
        return 'cmsbundle-manufacturer-detail';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $config = $slot->getFieldConfig();
        $collection = new CriteriaCollection();

        $manufacturer = $config->get('manufacturerId');


        if ($manufacturer->isStatic() && $manufacturer->getValue()) {
            $criteria = new Criteria([$manufacturer->getValue()]);
            $criteria->addAssociation('media');
            $collection->add(self::STATIC_MANUFACTURER_KEY_MANUAL . '_' . $slot->getUniqueIdentifier(), ProductManufacturerDefinition::class, $criteria);
        }


        return $collection->all() ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = new ManufacturerStruct();
        $slot->setData($data);

        $request = $resolverContext->getRequest();
        $context = $resolverContext->getSalesChannelContext();

        $config = $slot->getFieldConfig();
        $manufacturerConfig = $config->get('manufacturerId');

        if ($manufacturerConfig->isStatic()) {
            $searchResult = $result->get(self::STATIC_MANUFACTURER_KEY_MANUAL . '_' . $slot->getUniqueIdentifier());
            if ($searchResult === null) {
                return;
            }

            /** @var ProductManufacturerEntity $manufacturer */
            $manufacturer = $searchResult->getEntities()->first();
            if ($manufacturer === null) {
                return;
            }
        }

        $this->restrictFilters($slot, $request);

        if ($this->isCustomSorting($slot)) {
            $this->restrictSortings($request, $slot);
            $this->addDefaultSorting($request, $slot);
        }

        $criteria = new Criteria();
        $criteria->setTitle('cms::product-listing');

        $request->query->set('limit', 1000);
        $request->request->set('reduce-aggregations', ['manufacturer']);

        $this->processor?->prepare($request, $criteria, $context);

        $result = $this->listingRoute
            ->load($manufacturer->getId(), $request, $context, $criteria)
            ->getResult();

        $this->processor?->process($request, $result, $context);

        $data->setManufacturer($manufacturer);
        $data->setProducts($result);
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
        $defaults = ['rating-filter', 'shipping-free-filter', 'price-filter', 'property-filter'];

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
