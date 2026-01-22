<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Subscriber;

use Shopware\Core\Checkout\Cart\Event\CheckoutOrderPlacedEvent;
use Shopware\Core\Checkout\Cart\Order\CartConvertedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\RequestStack;
use TocafixCustomPlugin\Struct\CommissionsData;
use TocafixCustomPlugin\Struct\DeliveryDateDate;

class CheckoutSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityRepository
     */
    private $orderRepository;
    
    /**
     * @var CartConvertedEvent
     */
    protected $cartConvertedEvent;

    /**
     * @var RequestStack
     */
    private $request;

    public function __construct(
        EntityRepository $orderRepository,
        RequestStack $request,
    ) {
        $this->orderRepository = $orderRepository;
        $this->request = $request;
    }

    public static function getSubscribedEvents()
    {
        return [
            CartConvertedEvent::class => 'onCartConverted',
            CheckoutOrderPlacedEvent::class => 'onCheckoutOrderPlaced'
        ];
    }

    public function onCartConverted(CartConvertedEvent $event)
    {
        $this->cartConvertedEvent = $event;
    }

    public function onCheckoutOrderPlaced(CheckoutOrderPlacedEvent $event)
    {
        $order = $event->getOrder();

        $cart = $this->cartConvertedEvent->getCart();

        $currentRequest = $this->request->getCurrentRequest();
        /** @var InputBag $requests */
        $requests = $currentRequest->request;

        /** @var DeliveryDateDate $deliveryDateExtension */
        $deliveryDateExtension = $cart->getExtension('tocafix_delivery_date');

        $customFields = [
            "tocafix_delivery_date" => $deliveryDateExtension ? $deliveryDateExtension->getDeliveryDate() : "",
            "tocafix_customer_reference" => $requests->get("customerReference")
        ];

        if ($order->getCustomFields()) {
            $customFields = array_merge($customFields, $order->getCustomFields());
        }

        $this->orderRepository->update([
            [
                'id' => $order->getId(),
                'customFields' => $customFields
            ]
        ], $event->getContext());
    }
}
