<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use ShopwareBundlePlugin\Struct\CategoryStruct;
use Symfony\Component\HttpFoundation\Request;

#[Package('inventory')]
class CmsBundleCategoryListCmsElementResolver extends AbstractCmsElementResolver
{
    private const STATIC_SEARCH_KEY_MANUAL = 'category-list-manual';

    private readonly EntityRepository $categoryRepository;

    /**
     * @internal
     */
    public function __construct(EntityRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getType(): string
    {
        return 'cmsbundle-category-list';
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
            $collection->add(self::STATIC_SEARCH_KEY_MANUAL . '_' . $slot->getUniqueIdentifier(), CategoryDefinition::class, $criteria);
        }


        return $collection->all() ? $collection : null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $data = new CategoryStruct();
        $slot->setData($data);

        $request = $resolverContext->getRequest();
        $context = $resolverContext->getSalesChannelContext();

        $config = $slot->getFieldConfig();
        $categoryConfig = $config->get('categories');

        if ($categoryConfig->isStatic()) {
            $searchResult = $result->get(self::STATIC_SEARCH_KEY_MANUAL . '_' . $slot->getUniqueIdentifier());
            if ($searchResult === null) {
                return;
            }

            /** @var CategoryCollection|null $categories */
            $categories = $searchResult->getEntities();
            if ($categories === null) {
                return;
            }

            $data->setCategories($categories);
        } else {

            $navigationId = $this->getNavigationId($request, $context);

            $criteria = new Criteria();
            $criteria->setTitle('cms::cmsbundle-category-list');

            $listing = $this->loadSubCategories($navigationId, $context, $criteria);

            $data->setCategories($listing);
        }
    }

    private function loadSubCategories(string $categoryId, SalesChannelContext $context, Criteria $criteria)
    {
        $criteria->setTitle('category-listing-route::loading');

        $criteria->addFilter(
            new EqualsFilter('parentId', $categoryId)
        );

        $criteria->addAssociation("media");

        $results = $this->categoryRepository->search($criteria, $context->getContext());

        /** @var CategoryCollection|null $categories */
        $categories = $results->getEntities();

        return $categories;
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
}
