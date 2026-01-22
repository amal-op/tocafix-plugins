<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Storefront\Element;

use Shopware\Storefront\Page\Page;
use Shopware\Storefront\Framework\Page\StorefrontSearchResult;

class SubCategoryListPage extends Page
{
    protected StorefrontSearchResult $subCategory;

    protected ?string $searchTerm = null;
    protected ?string $navigationId = null;

    public function getSubCategory(): StorefrontSearchResult
    {
        return $this->subCategory;
    }

    public function setSubCategory(StorefrontSearchResult $subCategory): void
    {
        $this->subCategory = $subCategory;
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
