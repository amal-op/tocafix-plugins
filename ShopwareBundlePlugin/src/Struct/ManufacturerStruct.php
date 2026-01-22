<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Struct;

use Shopware\Core\Content\Product\Aggregate\ProductManufacturer\ProductManufacturerEntity;
use Shopware\Core\Content\Product\ProductCollection;
use Shopware\Core\Content\Product\SalesChannel\Listing\ProductListingResult;
use Shopware\Core\Framework\Struct\Struct;

class ManufacturerStruct extends Struct
{
    /**
     * @var ProductListingResult
     */
    protected $products;

    /**
     * @var ProductManufacturerEntity
     */
    protected $manufacturer;

    public function getManufacturer(): ?ProductManufacturerEntity
    {
        return $this->manufacturer;
    }

    public function setManufacturer(ProductManufacturerEntity $manufacturer): void
    {
        $this->manufacturer = $manufacturer;
    }

    public function getProducts(): ?ProductListingResult
    {
        return $this->products;
    }

    public function setProducts(ProductListingResult $products): void
    {
        $this->products = $products;
    }

    public function getApiAlias(): string
    {
        return 'cms_cmsbundle_manufacturer_detail';
    }
}
