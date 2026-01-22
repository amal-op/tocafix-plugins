<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Controller;

use Shopware\Core\Checkout\Cart\AbstractCartPersister;
use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use TocafixCustomPlugin\Struct\CommissionsData;

/**
 * @Route(defaults={"_routeScope"={"storefront"}})
 */
class CartLineItemController extends StorefrontController
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
     * @Route("/checkout/line-item/update-commission", name="frontend.checkout.line-item.update-commission", defaults={"XmlHttpRequest": true}, methods={"POST"})
     */
    public function updateCommission(Cart $cart, Request $request, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $commissionNumber = $request->request->get('commissionNo');
        $commissionName = $request->request->get('commissionName');

        if (strlen($commissionNumber) + strlen($commissionName) > 50) {
            return new JsonResponse([
                'success' => false,
                'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                    'type' => 'danger',
                    'content' => $this->trans('tocafix-custom.commission.error')
                ]),
            ]);
        }

        $cart->addExtension('tocafix_commission', new CommissionsData($commissionNumber, $commissionName));
        $this->cartPersister->save($cart, $salesChannelContext);

        return new JsonResponse([
            'success' => true,
            'alert' => $this->renderView('@Storefront/storefront/utilities/alert.html.twig', [
                'type' => 'success',
                'content' => $this->trans('tocafix-custom.commission.success')
            ]),
        ]);
    }
}
