<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Storefront\Element;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Framework\Page\StorefrontSearchResult;
use Shopware\Core\Content\Cms\DataResolver\FieldConfigCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use ShopwareBundlePlugin\Storefront\Element\SubCategoryListPage;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class SubCategoryListPageLoader
{
    private GenericPageLoader $genericLoader;
    private EntityRepository $categoryRepository;

    public function __construct(
        EntityRepository $categoryRepository,
        GenericPageLoader $genericLoader
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->genericLoader = $genericLoader;
    }

    public function load(Request $request, SalesChannelContext $salesChannelContext, FieldConfigCollection $config): SubCategoryListPage
    {
        $categoryId = $request->get('navigationId');
        $slectedOption = $config->get('selectedOption')->getValue();

        $slectedOption =='category' ? $dataFetch = true : $dataFetch = false;

        $categoryIds = $dataFetch ? $config->get('category')->getValue() : [$categoryId];
        $criteria = new Criteria();

        if($dataFetch) {
            $criteria->addFilter(new EqualsAnyFilter('id', $categoryIds));
        } else {
            $criteria->addFilter(new EqualsFilter('parentId', $categoryId));
        }
        
        $criteria->addAssociation('media');
        $criteria->addAssociation('customFields');
        $criteria->addFilter(new EqualsFilter('active', true));
        $category = $this->categoryRepository->search($criteria, $salesChannelContext->getContext())->getEntities();

        $page = SubCategoryListPage::createFrom($this->genericLoader->load($request, $salesChannelContext));
        $page->setNavigationId($categoryId);
        $page->setSubCategory(StorefrontSearchResult::createFrom($category));

        return $page;
    }
}
