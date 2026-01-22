<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Controller;

use Shopware\Core\Checkout\Cart\AbstractCartPersister;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use TocafixCustomPlugin\Struct\DeliveryDateDate;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class DeliveryDateController extends StorefrontController
{
    /**
     * @var AbstractCartPersister
     */
    protected $cartPersister;

    public function __construct(AbstractCartPersister $cartPersister)
    {
        $this->cartPersister = $cartPersister;
    }

    /**
     * @Route("/checkout/cart/add-delivery-date", name="frontend.checkout.delivery-date.add", defaults={"XmlHttpRequest": true}, methods={"POST"})
     */
    public function addDeliveryDate(Cart $cart, Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $deliveryDate = $request->request->get('deliveryDate');

        $cart->addExtension('tocafix_delivery_date', new DeliveryDateDate($deliveryDate));
        $this->cartPersister->save($cart, $salesChannelContext);

        return new JsonResponse(['success' => true]);
    }
}