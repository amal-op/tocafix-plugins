<?php declare(strict_types=1);

namespace VioCustomerPrice\Core\Content\Product\SalesChannel\Price;

use Shopware\Core\Checkout\Cart\Price\QuantityPriceCalculator;
use Shopware\Core\Checkout\Cart\Price\Struct\CalculatedPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\CartPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\ListPrice;
use Shopware\Core\Checkout\Cart\Price\Struct\PriceCollection;
use Shopware\Core\Checkout\Cart\Price\Struct\QuantityPriceDefinition;
use Shopware\Core\Checkout\Cart\Price\Struct\ReferencePriceDefinition;
use Shopware\Core\Checkout\Cart\Tax\Struct\TaxRuleCollection;
use Shopware\Core\Checkout\Cart\Tax\TaxCalculator;
use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\DataAbstractionLayer\CheapestPrice\CalculatedCheapestPrice;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Content\Product\SalesChannel\Price\AbstractProductPriceCalculator;
use Shopware\Core\Content\Product\SalesChannel\SalesChannelProductEntity;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use VioCustomerPrice\Core\Content\Product\ProductExtension;
use VioCustomerPrice\Entity\CustomerPriceCollection;
use VioCustomerPrice\Entity\CustomerPriceEntity;
use VioCustomerPrice\Event\CustomerPriceEvent;

class ProductPriceCalculator extends AbstractProductPriceCalculator
{
    public function __construct(
        private readonly AbstractProductPriceCalculator $coreService,
        private readonly SystemConfigService $configService,
        private readonly QuantityPriceCalculator $quantityPriceCalculator,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly TaxCalculator $taxCalculator,
    ) {
    }

    public function getDecorated(): AbstractProductPriceCalculator
    {
        return $this->coreService;
    }

    public function calculate(iterable $products, SalesChannelContext $context): void
    {
        $this->coreService->calculate($products, $context);
        $customer = $context->getCustomer();
        if ($customer instanceof CustomerEntity) {
            $ifDiscountIsSetUseCustomerPriceAsListPrice = $this->configService->getBool('VioCustomerPrice.config.ifDiscountIsSetUseCustomerPriceAsListPrice', $context->getSalesChannelId());
            $ignoreCalculatedPrices = $this->configService->getBool('VioCustomerPrice.config.ignoreCalculatedPrices', $context->getSalesChannelId());
            /** @var SalesChannelProductEntity $product */
            foreach ($products as $product) {
                if (!$ignoreCalculatedPrices) {
                    $originalCalculatedPrices = $product->getCalculatedPrices();
                    $originalCalculatedPrice = $originalCalculatedPrices->first();
                    if ($originalCalculatedPrice === null) {
                        $originalCalculatedPrice = $product->getCalculatedPrice();
                    }
                    $originalListPrice = $originalCalculatedPrice->getListPrice()?->getPrice();
                } else {
                    $price = $product->getPrice()?->getCurrencyPrice(
                        $context->getCurrencyId()
                    );
                    if ($price === null) {
                        continue;
                    }
                    $priceDefinition = $this->calcTax(new QuantityPriceDefinition(
                        $price->getNet(),
                        $context->buildTaxRules($product->getTaxId())
                    ), $context);
                    $originalCalculatedPrice = $this->quantityPriceCalculator->calculate($priceDefinition, $context);
                    $originalCalculatedPrices = new PriceCollection([$originalCalculatedPrice]);
                    $originalListPrice = $context->getTaxState() === CartPrice::TAX_STATE_GROSS
                        ? $price->getListPrice()?->getGross()
                        : $price->getListPrice()?->getNet()
                    ;
                }

                $originalCalculatedCheapestPrice = $product->getCalculatedCheapestPrice();
                // check if original price should be displayed as pseudo price
                if ($originalListPrice === null && $this->configService->getBool('VioCustomerPrice.config.originalAsListPrice', $context->getSalesChannelId())) {
                    $originalListPrice = $originalCalculatedPrice->getUnitPrice();
                }

                /** @var CustomerPriceCollection $customerPricesCollection */
                $customerPricesCollection = $product->getExtension( ProductExtension::CUSTOMER_PRICES_EXTENSION_KEY );
                if ($customerPricesCollection instanceof CustomerPriceCollection && $customerPricesCollection->first() !== null) {
                    // special case, if only one discount is set, apply it to all calculated prices
                    if (
                        $customerPricesCollection->count() === 1
                        && !$this->configService->getBool('VioCustomerPrice.config.deactivateSpecialCaseForOnlyOnePriceWithDiscount', $context->getSalesChannelId())
                        && $customerPricesCollection->first()->getQuantityEnd() === null
                        && $customerPricesCollection->first()->getPrice() === null
                        && $customerPricesCollection->first()->getDiscount() !== null
                        && ($customerPricesCollection->first()->getQuantityStart() === 1 || $customerPricesCollection->first()->getQuantityStart() === 0)
                    ) {
                        $discountPrice = $customerPricesCollection->first();
                        $listPrice = $originalListPrice;
                        if ($ifDiscountIsSetUseCustomerPriceAsListPrice
                            && $discountPrice->getDiscount() !== null && $discountPrice->getDiscount() !== 0.0
                            && $discountPrice->getPrice() !== null && $discountPrice->getPrice() !== 0.0
                        ) {
                            $listPrice = $discountPrice->getPrice();
                        }
                        // apply discount on all calculated prices
                        foreach ($originalCalculatedPrices as $price) {
                            $this->setPriceOnCalculatedPrice($price, $discountPrice, $listPrice, $context);
                        }

                        if ($originalCalculatedPrices->first() !== $originalCalculatedPrice) {
                            $this->setPriceOnCalculatedPrice($originalCalculatedPrice, $discountPrice, $listPrice, $context);
                        }

                        if ($product->getCalculatedCheapestPrice()) {
                            $this->setPriceOnCalculatedPrice($product->getCalculatedCheapestPrice(), $discountPrice, $listPrice, $context);
                        }

                        $product->setCalculatedPrice($originalCalculatedPrice);
                        $product->setCalculatedPrices($originalCalculatedPrices);

                        continue;
                    }

                    $prices = new PriceCollection();
                    $taxRules = $context->buildTaxRules($product->getTaxId());
                    $quantityCustomerPrice = $customerPricesCollection->first();
                    $cheapestPrice = null;

                    /** @var CustomerPriceEntity $customerPrice */
                    foreach ($customerPricesCollection as $customerPrice) {
                        $listPrice = $originalListPrice;
                        if ($ifDiscountIsSetUseCustomerPriceAsListPrice
                            && $customerPrice->getDiscount() !== null && $customerPrice->getDiscount() !== 0.0
                            && $customerPrice->getPrice() !== null && $customerPrice->getPrice() !== 0.0
                        ) {
                            $listPrice = $customerPrice->getPrice();
                        }
                        $curQuantity = $customerPrice->getQuantityEnd();
                        if ($curQuantity === 0 || $curQuantity === null) {
                            $curQuantity = $customerPrice->getQuantityStart();
                        }

                        $price = new QuantityPriceDefinition(
                            $this->calcCustomerPrice($customerPrice, $originalCalculatedPrice->getUnitPrice(), $context, $originalCalculatedPrice->getTaxRules()),
                            $taxRules,
                            $curQuantity
                        );
                        $price->setIsCalculated(true);
                        $price->setReferencePriceDefinition($this->buildReferencePriceDefinition($product));
                        if ($listPrice !== null) {
                            $price->setListPrice(
                                $listPrice
                            );
                        }
                        $prices->add(
                            $this->quantityPriceCalculator->calculate($price, $context)
                        );
                        if ($cheapestPrice === null || $price->getPrice() < $cheapestPrice->getPrice()) {
                            $cheapestPrice = $price;
                        }
                    }

                    $product->setCalculatedPrices($prices);

                    $quantityPrice = new QuantityPriceDefinition(
                        $this->calcCustomerPrice($quantityCustomerPrice, $originalCalculatedPrice->getUnitPrice(), $context, $originalCalculatedPrice->getTaxRules()),
                        $taxRules
                    );
                    $quantityPrice->setIsCalculated(true);
                    $quantityPrice->setReferencePriceDefinition($this->buildReferencePriceDefinition($product));
                    if ($originalListPrice !== null) {
                        $quantityPrice->setListPrice($originalListPrice);
                    }
                    $product->setCalculatedPrice(
                        $this->quantityPriceCalculator->calculate($quantityPrice, $context)
                    );

                    $calculatedCheapestPrice = CalculatedCheapestPrice::createFrom(
                        $this->quantityPriceCalculator->calculate($cheapestPrice, $context)
                    );
                    $product->setCalculatedCheapestPrice($calculatedCheapestPrice);
                }

                $event = new CustomerPriceEvent(
                    $product,
                    $customer,
                    $originalCalculatedPrices,
                    $originalCalculatedPrice,
                    $originalCalculatedCheapestPrice,
                    $context
                );
                $this->eventDispatcher->dispatch($event);
            }
        }
    }

    protected function setPriceOnCalculatedPrice(CalculatedPrice $price, CustomerPriceEntity $discountPrice, ?float $originalListPrice, SalesChannelContext $context): void
    {
        $reducedUnitPrice = $this->calcCustomerPrice($discountPrice, $price->getUnitPrice(), $context, $price->getTaxRules());
        $price->assign([
            'unitPrice' => $reducedUnitPrice,
            'totalPrice' => $this->calcCustomerPrice($discountPrice, $price->getTotalPrice(), $context, $price->getTaxRules()),
        ]);

        if ($originalListPrice !== null) {
            if ($price->getListPrice()) {
                $originalListPrice = $price->getListPrice()->getPrice();
            }
            $listPrice = ListPrice::createFromUnitPrice(
                $reducedUnitPrice,
                $originalListPrice
            );
            $price->assign(['listPrice' => $listPrice]);
        }
    }

    private function buildReferencePriceDefinition(ProductEntity $product): ?ReferencePriceDefinition
    {
        $referencePrice = null;
        if (
            $product->getPurchaseUnit()
            && $product->getReferenceUnit()
            && $product->getUnit() !== null
            && $product->getPurchaseUnit() !== $product->getReferenceUnit()
        ) {
            $referencePrice = new ReferencePriceDefinition(
                $product->getPurchaseUnit(),
                $product->getReferenceUnit(),
                (string) $product->getUnit()->getTranslation('name')
            );
        }

        return $referencePrice;
    }

    private function calcCustomerPrice(CustomerPriceEntity $customerPrice, float $originalPrice, SalesChannelContext $context, TaxRuleCollection $taxRules): float
    {
        $applyDiscountOnCustomerPriceItSelf = $this->configService->getBool('VioCustomerPrice.config.applyDiscountOnCustomerPriceItSelf', $context->getSalesChannelId());
        $customerPriceValue = null;
        if ($applyDiscountOnCustomerPriceItSelf
            && $customerPrice->getDiscount() !== null
            && $customerPrice->getDiscount() !== 0.0
            && $customerPrice->getPrice() > 0
        ) {
            $customerPriceValue = $customerPrice->getPrice() * (1 - ($customerPrice->getDiscount() / 100));
        }
        if ($customerPrice->getPrice() > 0) {
            $customerPriceValue = $customerPrice->getPrice();
        }
        if($customerPriceValue !== null) {
            if($context->getTaxState() === CartPrice::TAX_STATE_GROSS && $this->configService->getBool('VioCustomerPrice.config.customerPricesAreNet'))
            {
                return $this->taxCalculator->calculateGross(
                    $customerPriceValue,
                    $taxRules
                );
            }
            return $customerPriceValue;
        }
        if ($customerPrice->getDiscount() !== null && $customerPrice->getDiscount() !== 0.0) {
            return $originalPrice * (1 - ($customerPrice->getDiscount() / 100));
        }

        return $originalPrice;
    }

    private function calcTax(QuantityPriceDefinition $quantityPriceDefinition, SalesChannelContext $context): QuantityPriceDefinition
    {
        if ($context->getTaxState() === CartPrice::TAX_STATE_GROSS) {
            // calc tax
            $grossPrice = $this->taxCalculator->calculateGross(
                $quantityPriceDefinition->getPrice(),
                $quantityPriceDefinition->getTaxRules()
            );
            $quantityPriceDefinition = new QuantityPriceDefinition(
                $grossPrice,
                $quantityPriceDefinition->getTaxRules(),
                $quantityPriceDefinition->getQuantity()
            );
            $quantityPriceDefinition->setIsCalculated($quantityPriceDefinition->isCalculated());
            $quantityPriceDefinition->setReferencePriceDefinition($quantityPriceDefinition->getReferencePriceDefinition());
            $quantityPriceDefinition->setListPrice($quantityPriceDefinition->getListPrice());
        }

        return $quantityPriceDefinition;
    }
}
