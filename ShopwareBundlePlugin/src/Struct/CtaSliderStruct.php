<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Struct;

use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Framework\Struct\Collection;

class CtaSliderStruct extends Struct
{
    /**
     * @var Collection|CtaSliderItemStruct[]
     */
    protected $sliderItems;
    protected ?array $navigation = null;

    public function getSliderItems()
    {
        return $this->sliderItems;
    }

    public function addSliderItem(CtaSliderItemStruct $sliderItem): void
    {
        $this->sliderItems[] = $sliderItem;
    }

    public function getNavigation(): ?array
    {
        return $this->navigation;
    }

    public function setNavigation(?array $navigation): void
    {
        $this->navigation = $navigation;
    }

    public function getApiAlias(): string
    {
        return 'cms_bundle_cta_slider';
    }
}
