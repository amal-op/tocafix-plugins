<?php declare(strict_types=1);

namespace TocafixCustomPlugin\Core\Checkout\Cart\Order;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartException;
use Shopware\Core\Checkout\Cart\Error\ErrorCollection;
use Shopware\Core\Checkout\Cart\Exception\CustomerNotLoggedInException;
use Shopware\Core\Checkout\Cart\Exception\InvalidCartException;
use Shopware\Core\Checkout\Cart\Order\OrderConversionContext;
use Shopware\Core\Checkout\Cart\Order\OrderConverter;
use Shopware\Core\Checkout\Cart\Order\OrderPersisterInterface;
use Shopware\Core\Checkout\Order\Exception\DeliveryWithoutAddressException;
use Shopware\Core\Checkout\Order\Exception\EmptyCartException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\InconsistentCriteriaIdsException;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\RequestStack;
use TocafixCustomPlugin\Core\Checkout\Cart\Error\CustomCommissionBlockedError;

class OrderPersisterDecorator implements OrderPersisterInterface
{
    private const CUSTOM_FIELDS_KEY = 'customFields';

    private OrderPersisterInterface $decorated;

    private EntityRepository $orderRepository;

    private OrderConverter $converter;

    private RequestStack $request;

    /**
     * @internal
     */
    public function __construct(
        OrderPersisterInterface $decorated,
        EntityRepository $repository,
        OrderConverter $converter,
        RequestStack $request
    ) {
        $this->decorated = $decorated;
        $this->orderRepository = $repository;
        $this->converter = $converter;
        $this->request = $request;
    }

    /**
     * @throws CustomerNotLoggedInException
     * @throws DeliveryWithoutAddressException
     * @throws EmptyCartException
     * @throws InvalidCartException
     * @throws InconsistentCriteriaIdsException
     */
    public function persist(Cart $cart, SalesChannelContext $context): string
    {
        $currentRequest = $this->request->getCurrentRequest();
        /** @var InputBag $requests */
        $requests = $currentRequest->request;
        $commissionName = $requests->get("commissionName");
        $commissionNumber = $requests->get("commissionNo");

        if (!$commissionName && !$commissionNumber) {
            return $this->decorated->persist($cart, $context);
        }

        if (strlen($commissionNumber) + strlen($commissionName) > 50) {
            throw CartException::invalidCart(new ErrorCollection(
                [
                    new CustomCommissionBlockedError(Uuid::randomHex()),
                ]
            ));
            // to do this we need to add a new error
        }

        if (!$context->getCustomer()) {
            throw CartException::customerNotLoggedIn();
        }
        if ($cart->getLineItems()->count() <= 0) {
            throw new EmptyCartException();
        }

        $order = $this->converter->convertToOrder($cart, $context, new OrderConversionContext());

        $order[self::CUSTOM_FIELDS_KEY] = $this
            ->createCustomFields($order, $commissionName, $commissionNumber);

        $context->getContext()->scope(Context::SYSTEM_SCOPE, function (Context $context) use ($order): void {
            $this->orderRepository->create([$order], $context);
        });

        return $order['id'];
    }

    protected function createCustomFields(array $orderData, string $commissionName, string $commissionNumber): array
    {
        $customFields = [
            "tocafix_commissions_name" => $commissionName,
            "tocafix_commissions_number" => $commissionNumber,
        ];

        if (isset($orderData[self::CUSTOM_FIELDS_KEY])) {
            $customFields = array_merge($customFields, $orderData[self::CUSTOM_FIELDS_KEY]);
        }

        return $customFields;
    }
}
