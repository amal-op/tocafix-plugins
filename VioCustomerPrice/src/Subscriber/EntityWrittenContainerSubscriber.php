<?php

declare(strict_types=1);

namespace VioCustomerPrice\Subscriber;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenContainerEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use VioCustomerPrice\Entity\CustomerPriceEntity;

readonly class EntityWrittenContainerSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityRepository $entityRepository
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntityWrittenContainerEvent::class => 'onEntityWrittenContainerEvent',
        ];
    }

    /**
     * @throws InconsistentCriteriaIdsException
     * @throws \Exception
     */
    public function onEntityWrittenContainerEvent(EntityWrittenContainerEvent $event): void
    {
        foreach ($event->getEvents() as $nestedEvent) {
            if ($nestedEvent instanceof EntityWrittenEvent) {
                foreach ($nestedEvent->getWriteResults() as $entityWriteResult) {
                    if ($entityWriteResult->getEntityName() === 'vio_customer_price'
                        && $entityWriteResult->getExistence() !== null
                        && !\array_key_exists('quantityEnd', $entityWriteResult->getPayload())
                        && ($entityWriteResult->getOperation() === 'update' || $entityWriteResult->getOperation() === 'insert')
                    ) {
                        $this->reorderPrices($entityWriteResult->getPrimaryKey(), $event->getContext());
                    }
                }
            }
        }
    }

    /**
     * reorder the related prices to the given price
     *
     * @throws InconsistentCriteriaIdsException
     * @throws \Exception
     */
    private function reorderPrices(string $priceId, Context $context): void
    {
        /** @var CustomerPriceEntity $customerPrice */
        $customerPrice = $this->entityRepository->search(new Criteria([$priceId]), $context)->first();
        if ($customerPrice !== null) {
            $criteria = new Criteria();
            $criteria
                ->addFilter(new EqualsFilter('customerId', $customerPrice->getCustomerId()))
                ->addFilter(new EqualsFilter('productId', $customerPrice->getProductId()))
                ->addSorting(new FieldSorting('quantityStart', FieldSorting::ASCENDING));
            $prices = $this->entityRepository->search($criteria, $context);
            if ($prices->count() > 1) {
                /** @var CustomerPriceEntity|null $lastPrice */
                $lastPrice = null;
                $updateData = [];
                /** @var CustomerPriceEntity $curPrice */
                foreach ($prices as $curPrice) {
                    if ($lastPrice !== null) {
                        $updateData[] = ['id' => $lastPrice->getId(), 'quantityEnd' => $curPrice->getQuantityStart() - 1];
                    }
                    $lastPrice = $curPrice;
                }
                $this->entityRepository->update($updateData, $context);
            }
        }
    }
}
