<?php declare(strict_types=1);

namespace VioCustomerPrice\Entity;

use Shopware\Core\Checkout\Customer\CustomerEntity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCustomFieldsTrait;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class CustomerPriceEntity extends Entity
{
    use EntityCustomFieldsTrait;
    use EntityIdTrait;

    protected ?CustomerEntity $customer = null;

    protected string $customerId;

    protected ?ProductEntity $product = null;

    protected string $productId;

    protected string $productVersionId;

    protected int $quantityStart;

    protected ?int $quantityEnd = null;

    protected ?float $price = null;

    protected ?float $discount = null;

    public function getCustomer(): ?CustomerEntity
    {
        return $this->customer;
    }

    public function setCustomer(CustomerEntity $customer): CustomerPriceEntity
    {
        $this->customer = $customer;

        return $this;
    }

    public function getCustomerId(): string
    {
        return $this->customerId;
    }

    public function setCustomerId(string $customerId): CustomerPriceEntity
    {
        $this->customerId = $customerId;

        return $this;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product): CustomerPriceEntity
    {
        $this->product = $product;

        return $this;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): CustomerPriceEntity
    {
        $this->productId = $productId;

        return $this;
    }

    public function getProductVersionId(): string
    {
        return $this->productVersionId;
    }

    public function setProductVersionId(string $productVersionId): CustomerPriceEntity
    {
        $this->productVersionId = $productVersionId;

        return $this;
    }

    public function getQuantityStart(): int
    {
        return $this->quantityStart;
    }

    public function setQuantityStart(int $quantityStart): CustomerPriceEntity
    {
        $this->quantityStart = $quantityStart;

        return $this;
    }

    public function getQuantityEnd(): ?int
    {
        return $this->quantityEnd;
    }

    public function setQuantityEnd(?int $quantityEnd): CustomerPriceEntity
    {
        $this->quantityEnd = $quantityEnd;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): CustomerPriceEntity
    {
        $this->price = $price;

        return $this;
    }

    public function getDiscount(): ?float
    {
        return $this->discount;
    }

    public function setDiscount(?float $discount): CustomerPriceEntity
    {
        $this->discount = $discount;

        return $this;
    }
}
