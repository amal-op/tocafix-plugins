<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Struct;

use Shopware\Core\Content\Category\CategoryCollection;
use Shopware\Core\Framework\Struct\Struct;

class CategoryStruct extends Struct
{
    /**
     * @var CategoryCollection|null
     */
    protected $categories;

    public function getCategories(): ?CategoryCollection
    {
        return $this->categories;
    }

    public function setCategories(CategoryCollection $categories): void
    {
        $this->categories = $categories;
    }

    public function getApiAlias(): string
    {
        return 'cms_cmsbundle_category_slider';
    }
}
