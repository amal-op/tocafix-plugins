<?php /** @noinspection PhpMissingFieldTypeInspection */

declare(strict_types=1);

namespace VioCustomerPrice\Subscriber;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\ProductEvents;
use Shopware\Core\Framework\Api\Context\SalesChannelApiSource;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityLoadedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\PlatformRequest;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use VioCustomerPrice\Core\Content\Product\ProductExtension;


readonly class ProductSubscriber implements EventSubscriberInterface
{
    /**
     * ProductSubscriber constructor.
     */
    public function __construct(
        private RequestStack $requestStack,
        private EntityRepository $customerPriceRepository
    ) {
    }

    /**
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ProductEvents::PRODUCT_LOADED_EVENT => 'onProductLoaded',
        ];
    }

    public function onProductLoaded(EntityLoadedEvent $event): void
    {
        if ($event->getDefinition() instanceof ProductDefinition
            && $this->requestStack->getCurrentRequest() instanceof Request
        ) {
            $source = $event->getContext()->getSource();

            if ($source instanceof SalesChannelApiSource) {
                /** @var SalesChannelContext $salesChannelContext */
                $salesChannelContext = $this->requestStack->getCurrentRequest()->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);

                if ($salesChannelContext instanceof SalesChannelContext
                    && $salesChannelContext->getCustomer() instanceof CustomerEntity
                ) {
                    $customerPricesResult = $this->customerPriceRepository
                        ->search(
                            (new Criteria())
                            ->addFilter(
                                new EqualsFilter('customerId', $salesChannelContext->getCustomer()->getId()),
                                new EqualsAnyFilter('productId', $event->getIds())
                            ),
                            $event->getContext()
                        );

                    /** @var ProductEntity $product */
                    foreach ($event->getEntities() as $product) {
                        if (!$product->hasExtension(ProductExtension::CUSTOMER_PRICES_EXTENSION_KEY)) {
                            $customerPrices = $customerPricesResult->filterByProperty('productId', $product->getId());
                            $product->addExtension(ProductExtension::CUSTOMER_PRICES_EXTENSION_KEY, $customerPrices->getEntities());
                        }
                    }
                }
            }
        }
    }
}
