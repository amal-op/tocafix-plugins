<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Storefront\Element;

use Shopware\Core\Content\Product\Aggregate\ProductVisibility\ProductVisibilityDefinition;
use Shopware\Core\Content\Product\SalesChannel\Listing\AbstractProductListingRoute;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingLoader;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingRouteResponse;
use Shopware\Core\Content\Product\SalesChannel\ProductAvailableFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['store-api']])]
#[Package('inventory')]
class CmsBundleManufacturerPoductListingRoute extends AbstractProductListingRoute
{
    /**
     * @internal
     */
    public function __construct(
        private readonly ProductListingLoader $listingLoader
    ) {
    }

    public function getDecorated(): AbstractProductListingRoute
    {
        throw new DecorationPatternException(self::class);
    }

    #[Route(path: '/store-api/manufacturer-product-listing/{manufacturerId}', name: 'store-api.manufacturer.product.listing', methods: ['POST'], defaults: ['_entity' => 'product', 'XmlHttpRequest' => true])]
    public function load(string $manufacturerId, Request $requests, SalesChannelContext $context, Criteria $criteria): ProductListingRouteResponse
    {
        $criteria->addFilter(
            new ProductAvailableFilter($context->getSalesChannel()->getId(), ProductVisibilityDefinition::VISIBILITY_ALL)
        );
        $criteria->setTitle('manufacturer-product-listing-route::loading');

        $criteria->addFilter(new EqualsFilter('product.manufacturerId', $manufacturerId));

        $entities = $this->listingLoader->load($criteria, $context);

        $result = ProductListingResult::createFrom($entities);
        $result->addState(...$entities->getStates());

        return new ProductListingRouteResponse($result);
    }
}
