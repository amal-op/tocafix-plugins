<?php

declare(strict_types=1);

namespace TocafixTheme\Subscriber;

use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OrderPlacedSubscriber implements EventSubscriberInterface
{
    /**
     * @var CartConvertedEvent
     */
    protected $cartConvertedEvent;

    public static function getSubscribedEvents()
    {
        return [
            CartConvertedEvent::class => 'onCartConverted'
        ];
    }

    public function onCartConverted(CartConvertedEvent $event)
    {
        $this->cartConvertedEvent = $event;
        $cart = $this->cartConvertedEvent->getCart();

        /** @var AdditionalShippingData $additionalShippingExtension */
        $additionalShippingExtension = $cart->getExtension('tocafixShippingCriteria');

        $convertedCart = $event->getConvertedCart();
        $convertedCart['customFields']['shippingType'] = $additionalShippingExtension ? $additionalShippingExtension->getShippingType() : '';

        $event->setConvertedCart($convertedCart);
    }
}