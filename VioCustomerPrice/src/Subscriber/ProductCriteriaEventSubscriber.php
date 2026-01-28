<?php
declare(strict_types=1);

namespace VioCustomerPrice\Subscriber;

use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\Event\SalesChannelProcessCriteriaEvent;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductCriteriaEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sales_channel.product.process.criteria' => 'onProductCriteria',
        ];
    }

    public function onProductCriteria(SalesChannelProcessCriteriaEvent $event): void
    {
        $this->addCustomerPricesAssociation(
            $event->getCriteria(),
            $event->getSalesChannelContext()
        );
    }

    protected function addCustomerPricesAssociation(Criteria $criteria, SalesChannelContext $context): void
    {
        if ($context->getCustomer()) {
            $criteria->getAssociation('customerPrices')
                ->addFilter(new EqualsFilter('customerId', $context->getCustomer()->getId()))
                ->addSorting(new FieldSorting('quantityStart'));
        }
    }
}
