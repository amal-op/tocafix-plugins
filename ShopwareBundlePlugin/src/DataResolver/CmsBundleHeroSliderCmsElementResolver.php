<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use ShopwareBundlePlugin\Struct\MediaStruct;

class CmsBundleHeroSliderCmsElementResolver extends AbstractCmsElementResolver
{
    public function __construct(
        private EntityRepository $mediaRepository
    ) {
    }

    public function getType(): string
    {
        return 'cmsbundle-hero-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $context = $resolverContext->getSalesChannelContext()->getContext();
        $mediaStruct = new MediaStruct();
        $slot->setData($mediaStruct);
        $fieldConfigDesktop = $slot->getFieldConfig()->get('mediaDesktop');
        $fieldConfigTablet = $slot->getFieldConfig()->get('mediaTablet');
        $fieldConfigMobile = $slot->getFieldConfig()->get('mediaMobile');
        $fieldConfigBackgroundDesktop = $slot->getFieldConfig()->get('backgroundDesktop');
        $fieldConfigBackgroundTablet = $slot->getFieldConfig()->get('backgroundTablet');
        $fieldConfigBackgroundMobile = $slot->getFieldConfig()->get('backgroundMobile');

        $imageId = $fieldConfigDesktop !== null ? $fieldConfigDesktop->getValue() : '';
        $imagemediaTabletId = $fieldConfigTablet !== null ? $fieldConfigTablet->getValue() : '';
        $imagemediaMobileId = $fieldConfigMobile !== null ? $fieldConfigMobile->getValue() : '';
        $backgroundImageId = $fieldConfigBackgroundDesktop !== null ? $fieldConfigBackgroundDesktop->getValue() : '';
        $backgroundImagemediaTabletId = $fieldConfigBackgroundTablet !== null ? $fieldConfigBackgroundTablet->getValue() : '';
        $backgroundImagemediaMobileId = $fieldConfigBackgroundMobile !== null ? $fieldConfigBackgroundMobile->getValue() : '';

        if ($imageId) {
            $media = $this->getImageById($imageId, $context);
            $mediaStruct->setMedia($media);
        }
        if ($imagemediaTabletId) {
            $mediaTablet = $this->getImageById($imagemediaTabletId, $context);
            $mediaStruct->setMediaTablet($mediaTablet);
        }
        if ($imagemediaMobileId) {
            $mediaMobile = $this->getImageById($imagemediaMobileId, $context);
            $mediaStruct->setMediaMobile($mediaMobile);
        }
        if ($backgroundImageId) {
            $media = $this->getImageById($backgroundImageId, $context);
            $mediaStruct->setBackgroundDesktop($media);
        }
        if ($backgroundImagemediaTabletId) {
            $mediaTablet = $this->getImageById($backgroundImagemediaTabletId, $context);
            $mediaStruct->setBackgroundTablet($mediaTablet);
        }
        if ($backgroundImagemediaMobileId) {
            $mediaMobile = $this->getImageById($backgroundImagemediaMobileId, $context);
            $mediaStruct->setBackgroundMobile($mediaMobile);
        }
    }

    public function getImageById(string $imageId, Context $context): ?MediaEntity
    {
        $criteria = new Criteria([$imageId]);

        return $this->mediaRepository->search($criteria, $context)->first();
    }
}
