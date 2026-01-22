<?php

declare(strict_types=1);

namespace  ShopwareBundlePlugin\Storefront\Element;

use Shopware\Storefront\Page\Page;
use Shopware\Storefront\Framework\Page\StorefrontSearchResult;

class CmsBundleBestSellerListPage extends Page
{
    protected StorefrontSearchResult $product;

    protected ?string $searchTerm = null;
    protected ?string $navigationId = null;

    public function getProduct(): StorefrontSearchResult
    {
        return $this->product;
    }

    public function setProduct(StorefrontSearchResult $product): void
    {
        $this->product = $product;
    }

    public function getNavigationId(): ?string
    {
        return $this->navigationId;
    }

    public function setNavigationId(?string $navigationId): void
    {
        $this->navigationId = $navigationId;
    }

    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    public function setSearchTerm(?string $searchTerm): void
    {
        $this->searchTerm = $searchTerm;
    }
}
