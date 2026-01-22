<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use ShopwareBundlePlugin\Storefront\Element\SubCategoryListPageLoader;

class CmsBundleSubCategoryListCmsElementResolver extends AbstractCmsElementResolver
{
    private readonly SubCategoryListPageLoader $subCategoryListPageLoader;

    /**
     * @internal
     */
    public function __construct(SubCategoryListPageLoader $subCategoryListPageLoader)
    {
        $this->subCategoryListPageLoader = $subCategoryListPageLoader;
    }

    public function getType(): string
    {
        return 'cmsbundle-subcategory-list';
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
   
        $page = $this->subCategoryListPageLoader->load($request, $salesChannelContext, $config, $result);
        
        if($page) {
            $slot->setData($page);
        }
    }

}
