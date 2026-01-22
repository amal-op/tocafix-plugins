<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Core\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartValidatorInterface;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RequestStack;
use TocafixCustomPlugin\Core\Checkout\Cart\Error\CustomCommissionBlockedError;

class CommissionCartValidator implements CartValidatorInterface
{
    private RequestStack $request;

    public function __construct(RequestStack $request)
    {
        $this->request = $request;
    }

    public function validate(Cart $cart, ErrorCollection $errorCollection, SalesChannelContext $salesChannelContext): void
    {
        $currentRequest = $this->request->getCurrentRequest();
        /** @var InputBag $requests */
        $requests = $currentRequest->request;
        $commissionName = $requests->get("commissionName");
        $commissionNumber = $requests->get("commissionNo");

        if ($commissionName || $commissionNumber) {
            if (strlen($commissionNumber) + strlen($commissionName) > 50) {
                $errorCollection->add(new CustomCommissionBlockedError($cart->getToken()));
                return;
            }
        }
    }
}