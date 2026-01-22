<?php declare(strict_types=1);

namespace TocafixTheme\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayEntity;

class TeamsElementResolver extends AbstractCmsElementResolver
{
    private $teamRepository;

    public function __construct(EntityRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    public function getType(): string
    {
        return 'tocafixteams';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();

        $categoryId = $config->has('category') ? $config->get('category')->getValue() : '00000000000000000000000000000000';
        $categoryId = $categoryId != null ? $categoryId : '00000000000000000000000000000000';
        $categoryId = $resolverContext->getRequest()->get('c', $categoryId);

        $maxNumberOfPosts = (int)$config->get('numberOfPosts')->getValue();

        $criteria = new Criteria();
        $criteria->addAssociation('teamCategories');
        $criteria->addSorting(new FieldSorting('sortOrder', FieldSorting::ASCENDING));
        $criteria->setLimit($maxNumberOfPosts);

        if($categoryId && $categoryId != '00000000000000000000000000000000') {
            $criteria->addFilter(new MultiFilter(MultiFilter::CONNECTION_OR, [
                new EqualsAnyFilter('teamCategories.id', [$categoryId]),
            ]));
        }

        $data = new ArrayEntity();
        $slot->setData($data);

        $result = $this->teamRepository->search($criteria, $resolverContext->getSalesChannelContext()->getContext());

        $data->set('result', $result);
    }
}