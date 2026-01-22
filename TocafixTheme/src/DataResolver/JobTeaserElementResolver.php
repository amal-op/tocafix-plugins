<?php

declare(strict_types=1);

namespace TocafixTheme\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\Struct\ArrayEntity;

class JobTeaserElementResolver
{
    private $jobRepository;

    public function __construct(EntityRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType(): string
    {
        return 'tocafix-job-teaser';
    }


    /**
     * Collect Data
     *
     * @param CmsSlotEntity $slot
     * @param ResolverContext $resolverContext
     * @return CriteriaCollection|null
     */
    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    /**
     * Enrich Data
     *
     * @param  CmsSlotEntity $slot
     * @param  ResolverContext $resolverContext
     * @param  ElementDataCollection $result
     * @return void
     */
    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $jobConfig = $slot->getFieldConfig()->get('job');

        $jobIds = $jobConfig->getValue();

        if ($jobIds === null) {
            return;
        }

        $data = new ArrayEntity();
        $slot->setData($data);

        $result = $this->getJobsByIds($jobIds, $resolverContext);

        $data->set('result', $result);
    }

    /**
     * Get job by id
     *
     * @param array $jobIds
     * @return EntitySearchResult|null
     */
    public function getJobsByIds(array $jobIds, ResolverContext $resolverContext): ?EntitySearchResult
    {
        $criteria = new Criteria($jobIds);
        $criteria->addAssociation('media');

        return $this->jobRepository->search($criteria, $resolverContext->getSalesChannelContext()->getContext());
    }
}
