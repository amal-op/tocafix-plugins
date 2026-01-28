<?php
/** @noinspection PhpUnused */
declare(strict_types=1);

namespace VioCustomerPrice\Event;

use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\DataAbstractionLayer\CheapestPrice\CalculatedCheapestPrice;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Contracts\EventDispatcher\Event;

class CustomerPriceEvent extends Event
{
    public function __construct(
        protected readonly SalesChannelProductEntity $product,
        protected readonly CustomerEntity $customer,
        protected readonly PriceCollection $originalCalculatedPrices,
        protected readonly CalculatedPrice $originalCalculatedPrice,
        protected readonly CalculatedCheapestPrice $originalCalculatedCheapestPrice,
        protected readonly SalesChannelContext $context
    ) {
    }

    public function getProduct(): SalesChannelProductEntity
    {
        return $this->product;
    }

    public function getCustomer(): CustomerEntity
    {
        return $this->customer;
    }

    public function getOriginalCalculatedPrices(): PriceCollection
    {
        return $this->originalCalculatedPrices;
    }

    public function getOriginalCalculatedPrice(): CalculatedPrice
    {
        return $this->originalCalculatedPrice;
    }

    public function getOriginalCalculatedCheapestPrice(): CalculatedCheapestPrice
    {
        return $this->originalCalculatedCheapestPrice;
    }

    public function getContext(): SalesChannelContext
    {
        return $this->context;
    }
}
