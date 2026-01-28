<?php

declare(strict_types=1);

namespace VioCustomerPrice\Subscriber;

use Shopware\Core\Content\Product\Events\InvalidateProductCache;
use Shopware\Core\Content\Product\SalesChannel\Detail\CachedProductDetailRoute;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Bucket\TermsAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Bucket\TermsResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class CacheSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityRepository $customerPriceRepository,
        private EntityRepository $productRepository,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'vio_customer_price.written' => 'onCustomerPriceWritten',
        ];
    }

    public function onCustomerPriceWritten(EntityWrittenEvent $event): void
    {
        /** @var TermsResult $productIdsAggregation */
        $productIdsAggregation = $this->customerPriceRepository->aggregate(
            (new Criteria($event->getIds()))
                ->addAggregation(
                    new TermsAggregation(
                        'productIds',
                        'productId'
                    )
                ),
            $event->getContext()
        )->get('productIds');

        $productIds = array_map(static fn ($productIdBucket) => $productIdBucket->getKey(), $productIdsAggregation->getBuckets());
        $productIds = array_filter($productIds);
        $productIds = array_unique($productIds);

        /** @var TermsResult $parentIdsResult */
        $parentIdsResult = $this->productRepository
            ->aggregate(
                (new Criteria($productIds))
                    ->addAggregation(
                        new TermsAggregation(
                            'parentIds',
                            'parentId'
                        )
                    ),
                $event->getContext()
            )->get('parentIds');
        $parentIds = array_map(static fn ($productIdBucket) => $productIdBucket->getKey(), $parentIdsResult->getBuckets());
        $parentIds = array_filter($parentIds);
        $parentIds = array_unique($parentIds);
        $productIds = array_unique([...$productIds, ...$parentIds]);

        $this->eventDispatcher->dispatch(new InvalidateProductCache($productIds));
    }
}
