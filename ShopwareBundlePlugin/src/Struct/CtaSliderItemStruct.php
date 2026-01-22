<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Struct;

use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Content\Media\MediaEntity;

class CtaSliderItemStruct extends Struct
{
    protected ?MediaEntity $media = null;
    protected ?string $title = null;
    protected ?string $buttonText = null;
    protected ?string $buttonUrl = null;
    protected ?string $url = null;
    protected ?string $buttonType = null;
    protected ?string $slideTag = null;

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getButtonText(): ?string
    {
        return $this->buttonText;
    }

    public function setButtonText(?string $buttonText): void
    {
        $this->buttonText = $buttonText;
    }

    public function getButtonUrl(): ?string
    {
        return $this->buttonUrl;
    }

    public function setButtonUrl(?string $buttonUrl): void
    {
        $this->buttonUrl = $buttonUrl;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public function getButtonType(): ?string
    {
        return $this->buttonType;
    }

    public function setButtonType(?string $buttonType): void
    {
        $this->buttonType = $buttonType;
    }

    public function getSlideTag(): ?string
    {
        return $this->slideTag;
    }

    public function setSlideTag(?string $slideTag): void
    {
        $this->slideTag = $slideTag;
    }

    public function getApiAlias(): string
    {
        return 'cms_bundle_image_three_grid';
    }
}
