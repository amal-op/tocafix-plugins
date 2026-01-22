<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use ShopwareBundlePlugin\Storefront\Element\CmsBundleBestSellerListPageLoader;

class CmsBundleBestSellerSliderCmsElementResolver extends AbstractCmsElementResolver
{
    private readonly CmsBundleBestSellerListPageLoader $bestSellerListPageLoader;

    /**
     * @internal
     */
    public function __construct(CmsBundleBestSellerListPageLoader $bestSellerListPageLoader)
    {
        $this->bestSellerListPageLoader = $bestSellerListPageLoader;
    }

    public function getType(): string
    {
        return 'cmsbundle-best-seller-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $salesChannelContext = $resolverContext->getSalesChannelContext();

        $request = $resolverContext->getRequest();

        $config = $slot->getFieldConfig();
   
        $page = $this->bestSellerListPageLoader->load($request, $salesChannelContext, $config);

        if($page) {
            $slot->setData($page);
        }
    }

}
