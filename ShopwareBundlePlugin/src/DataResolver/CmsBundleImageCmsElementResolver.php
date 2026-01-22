<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\DataResolver;

use ShopwareBundlePlugin\Struct\MediaStruct;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\Context;

class CmsBundleImageCmsElementResolver extends AbstractCmsElementResolver
{
    private EntityRepository $mediaRepository;


    public function __construct(
        EntityRepository $mediaRepository
    ) {
        $this->mediaRepository = $mediaRepository;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType(): string
    {
        return 'cmsbundle-image';
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

        $imageId = $fieldConfigDesktop !== null ? $fieldConfigDesktop->getValue() : '';
        $imagemediaTabletId = $fieldConfigTablet !== null ? $fieldConfigTablet->getValue() : '';
        $imagemediaMobileId = $fieldConfigMobile !== null ? $fieldConfigMobile->getValue() : '';

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
    }

    public function getImageById(string $imageId, Context $context): ?MediaEntity
    {
        $criteria = new Criteria([$imageId]);

        return $this->mediaRepository->search($criteria, $context)->first();
    }
}
