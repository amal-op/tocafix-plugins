<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\EntityResolverContext;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use ShopwareBundlePlugin\Struct\CtaSliderItemStruct;
use ShopwareBundlePlugin\Struct\CtaSliderStruct;
use Shopware\Core\Content\Media\Cms\AbstractDefaultMediaResolver;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class CmsBundleCtaSliderCmsElementResolver extends AbstractCmsElementResolver
{
    /**
     * @internal
     */
    public function __construct(private readonly AbstractDefaultMediaResolver $mediaResolver)
    {
    }

    public function getType(): string
    {
       return ''; // return 'cmsbundle-cta-slider';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $sliderItemsConfig = $slot->getFieldConfig()->get('sliderItems');

        if ($sliderItemsConfig === null || $sliderItemsConfig->isMapped() || $sliderItemsConfig->isDefault()) {
            return null;
        }

        $sliderItems = $sliderItemsConfig->getArrayValue();
        $mediaIds = array_column($sliderItems, 'mediaId');

        $criteria = new Criteria($mediaIds);

        $criteriaCollection = new CriteriaCollection();
        $criteriaCollection->add('media_' . $slot->getUniqueIdentifier(), MediaDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {
        $config = $slot->getFieldConfig();
        $ctaSlider = new CtaSliderStruct();
        $slot->setData($ctaSlider);

        $request = $resolverContext->getRequest();
        
        if ($request->get('navigationId') != null) {
            $ctaSlider->setNavigation($request->get('navigationId'));
        }

        $sliderItemsConfig = $config->get('sliderItems');
        if ($sliderItemsConfig === null) {
            return;
        }

        if ($sliderItemsConfig->isStatic()) {
            foreach ($sliderItemsConfig->getArrayValue() as $sliderItem) {
                $this->addMedia($slot, $ctaSlider, $result, $sliderItem);
            }
        }

        if ($sliderItemsConfig->isMapped() && $resolverContext instanceof EntityResolverContext) {
            $sliderItems = $this->resolveEntityValue($resolverContext->getEntity(), $sliderItemsConfig->getStringValue());

            if ($sliderItems === null || (is_countable($sliderItems) ? \count($sliderItems) : 0) < 1) {
                return;
            }

            foreach ($sliderItems->getMedia() as $media) {
                $ctaSliderItem = new CtaSliderItemStruct();
                $ctaSliderItem->setMedia($media);
                $ctaSlider->addSliderItem($ctaSliderItem);
            }
        }
    }

    private function addMedia(CmsSlotEntity $slot, CtaSliderStruct $ctaSlider, ElementDataCollection $result, array $config): void
    {
        $ctaSliderItem = new CtaSliderItemStruct();
        $ctaSliderItem->setUrl($config['url']);
        $ctaSliderItem->setTitle($config['slideTitle']);
        $ctaSliderItem->setButtonText($config['buttonText']);
        $ctaSliderItem->setButtonUrl($config['buttonUrl']);
        $ctaSliderItem->setButtonType($config['buttonType']);
        $ctaSliderItem->setSlideTag($config['slideTag']);
        $searchResult = $result->get('media_' . $slot->getUniqueIdentifier());
        if (!$searchResult) {
            return;
        }

        /** @var MediaEntity|null $media */
        $media = $searchResult->get($config['mediaId']);
        if (!$media) {
            return;
        }

        $ctaSliderItem->setMedia($media);
        $ctaSlider->addSliderItem($ctaSliderItem);
    }
}
