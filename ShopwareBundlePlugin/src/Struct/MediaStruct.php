<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Struct;

use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\Struct\Struct;


class MediaStruct extends Struct
{
    /**
     * @var MediaEntity|null
     */
    protected $mediaDesktop;
    protected $mediaTablet;
    protected $mediaMobile;
    protected $backgroundDesktop;
    protected $backgroundTablet;
    protected $backgroundMobile;

    /**
     * @return MediaEntity|null
     */
    public function getMediaDesktop(): ?MediaEntity
    {
        return $this->mediaDesktop;
    }

    public function getMediaTablet(): ?MediaEntity
    {
        return $this->mediaTablet;
    }
    public function getMediaMobile(): ?MediaEntity
    {
        return $this->mediaMobile;
    }

    public function getBackgroundDesktop(): ?MediaEntity
    {
        return $this->backgroundDesktop;
    }

    public function getBackgroundMobile(): ?MediaEntity
    {
        return $this->backgroundMobile;
    }

    public function getBackgroundTablet(): ?MediaEntity
    {
        return $this->backgroundTablet;
    }

    /**
     * @param MediaEntity|null $media
     */
    public function setMedia(?MediaEntity $mediaDesktop): void
    {
        $this->mediaDesktop = $mediaDesktop;
    }
    public function setMediaTablet(?MediaEntity $mediaTablet): void
    {
        $this->mediaTablet = $mediaTablet;
    }
    public function setMediaMobile(?MediaEntity $mediaMobile): void
    {
        $this->mediaMobile = $mediaMobile;
    }
    public function setBackgroundDesktop(?MediaEntity $backgroundDesktop): void
    {
        $this->backgroundDesktop = $backgroundDesktop;
    }
    public function setBackgroundMobile(?MediaEntity $backgroundMobile): void
    {
        $this->backgroundMobile = $backgroundMobile;
    }
    public function setBackgroundTablet(?MediaEntity $backgroundTablet): void
    {
        $this->backgroundTablet = $backgroundTablet;
    }
}
