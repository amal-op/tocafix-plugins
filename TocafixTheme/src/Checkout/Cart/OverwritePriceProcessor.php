<?php

declare(strict_types=1);

namespace TocafixTheme\Checkout\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Checkout\Cart\CartBehavior;
use Shopware\Core\Checkout\Cart\CartDataCollectorInterface;
use Shopware\Core\Checkout\Cart\CartProcessorInterface;
use Shopware\Core\Checkout\Cart\Delivery\DeliveryProcessor;
use Shopware\Core\Checkout\Cart\Delivery\Struct\Delivery;
use Shopware\Core\Checkout\Cart\LineItem\CartDataCollection;
use Shopware\Core\Checkout\Cart\LineItem\LineItem;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Tax\Struct\CalculatedTaxCollection;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Checkout\Shipping\ShippingMethodEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use TocafixTheme\Util\AdditionalShippingData;

class OverwritePriceProcessor implements CartDataCollectorInterface, CartProcessorInterface
{
    public function collect(CartDataCollection $data, Cart $original, SalesChannelContext $context, CartBehavior $behavior): void
    {
        // get all product line items
        $products = $original->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);

        if ($products->count() === 0) {
            return;
        }

        if (!$original->getDeliveries()->first()) {
            return;
        }

        if ($original->hasExtension("tocafixShippingCriteria")) {
            $original->removeExtension('tocafixShippingCriteria');
        }

        if ($original->hasExtension(DeliveryProcessor::MANUAL_SHIPPING_COSTS)) {
            $original->removeExtension(DeliveryProcessor::MANUAL_SHIPPING_COSTS);
        }

        $key = $this->buildKey($original->getToken().'-'.$original->getPrice()->getTotalPrice());

        $data->set($key, $original->getDeliveries()->first());
    }
    public function process(CartDataCollection $data, Cart $original, Cart $toCalculate, SalesChannelContext $context, CartBehavior $behavior): void
    {
        // get all product line items
        $products = $toCalculate->getLineItems()->filterType(LineItem::PRODUCT_LINE_ITEM_TYPE);

        if ($products->count() === 0) {
            return;
        }

        $key = $this->buildKey($original->getToken().'-'.$original->getPrice()->getTotalPrice());

        if (!$data->has($key) || $data->get($key) === null) {
            return;
        }

        /** @var Delivery $originalDelivery */
        $originalDelivery = $data->get($key);
        $shippingMethod = $originalDelivery->getShippingMethod();

        if (!$shippingMethod instanceof ShippingMethodEntity) {
            return;
        }

        if ($shippingMethod->getName() === 'Standard - AbLager' || $shippingMethod->getName() === 'Express') {
            $toCalculate->addExtension("tocafixShippingCriteria", new AdditionalShippingData("costShipping"));

            return;
        }

        if ($shippingMethod->getName() === 'Abholung') {
            $toCalculate->addExtension("tocafixShippingCriteria", new AdditionalShippingData("freeShipping"));

            return;
        }
        
        if (substr($shippingMethod->getName(), 0, 10) === 'Standard -') {
            if ($originalDelivery->getShippingCosts()->getTotalPrice() > 0) {
                $toCalculate->addExtension("tocafixShippingCriteria", new AdditionalShippingData("costShipping"));
                $toCalculate->addExtension(DeliveryProcessor::MANUAL_SHIPPING_COSTS, $this->createCalculatedPrice(0.01));
            } else {
                $toCalculate->addExtension("tocafixShippingCriteria", new AdditionalShippingData("freeShipping"));
            }
        }
    }

    private function buildKey(string $id): string
    {
        return 'price-overwrite-'.$id;
    }

    private function createCalculatedPrice(float $cost): CalculatedPrice
    {
        return new CalculatedPrice($cost, $cost, new CalculatedTaxCollection(), new TaxRuleCollection());
    }
}