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
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use ShopwareBundlePlugin\Struct\ProductAndMediaStruct;

class CmsBundleProductCtaCmsElementResolver extends AbstractCmsElementResolver
{
    private EntityRepository $mediaRepository;
    private SalesChannelRepository $productRepository;

    public function __construct(
        EntityRepository $mediaRepository,
        SalesChannelRepository $productRepository
    ) {
        $this->mediaRepository = $mediaRepository;
        $this->productRepository = $productRepository;
    }

    /**
     * Get Type
     *
     * @return string
     */
    public function getType(): string
    {
        return 'cmsbundle-product-cta';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

        $context = $resolverContext->getSalesChannelContext()->getContext();
        $ProductAndMediaStruct = new ProductAndMediaStruct();
        $slot->setData($ProductAndMediaStruct);

        $fieldConfigDesktop = $slot->getFieldConfig()->get('mediaDesktop');
        $fieldConfigTablet = $slot->getFieldConfig()->get('mediaTablet');
        $fieldConfigMobile = $slot->getFieldConfig()->get('mediaMobile');
        $fieldConfigBackgroundDesktop = $slot->getFieldConfig()->get('backgroundDesktop');
        $fieldConfigBackgroundTablet = $slot->getFieldConfig()->get('backgroundTablet');
        $fieldConfigBackgroundMobile = $slot->getFieldConfig()->get('backgroundMobile');

        $productId = $slot->getFieldConfig()->get('product')?->getValue() ?? '';
        if ($productId && $product = $this->getProductById($productId, $resolverContext->getSalesChannelContext())) {
            $ProductAndMediaStruct->setProduct($product);
        }
        
        $imageId = $fieldConfigDesktop !== null ? $fieldConfigDesktop->getValue() : '';
        $imagemediaTabletId = $fieldConfigTablet !== null ? $fieldConfigTablet->getValue() : '';
        $imagemediaMobileId = $fieldConfigMobile !== null ? $fieldConfigMobile->getValue() : '';
        $backgroundImageId = $fieldConfigBackgroundDesktop !== null ? $fieldConfigBackgroundDesktop->getValue() : '';
        $backgroundImagemediaTabletId = $fieldConfigBackgroundTablet !== null ? $fieldConfigBackgroundTablet->getValue() : '';
        $backgroundImagemediaMobileId = $fieldConfigBackgroundMobile !== null ? $fieldConfigBackgroundMobile->getValue() : '';

        if ($imageId) {
            $media = $this->getImageById($imageId, $context);
            $ProductAndMediaStruct->setMedia($media);
        }
        if ($imagemediaTabletId) {
            $mediaTablet = $this->getImageById($imagemediaTabletId, $context);
            $ProductAndMediaStruct->setMediaTablet($mediaTablet);
        }
        if ($imagemediaMobileId) {
            $mediaMobile = $this->getImageById($imagemediaMobileId, $context);
            $ProductAndMediaStruct->setMediaMobile($mediaMobile);
        }
        if ($backgroundImageId) {
            $media = $this->getImageById($backgroundImageId, $context);
            $ProductAndMediaStruct->setBackgroundDesktop($media);
        }
        if ($backgroundImagemediaTabletId) {
            $mediaTablet = $this->getImageById($backgroundImagemediaTabletId, $context);
            $ProductAndMediaStruct->setBackgroundTablet($mediaTablet);
        }
        if ($backgroundImagemediaMobileId) {
            $mediaMobile = $this->getImageById($backgroundImagemediaMobileId, $context);
            $ProductAndMediaStruct->setBackgroundMobile($mediaMobile);
        }
    }

    public function getImageById(string $imageId, Context $context): ?MediaEntity
    {
        $criteria = new Criteria([$imageId]);

        return $this->mediaRepository->search($criteria, $context)->first();
    }

    public function getProductById(string $productId, SalesChannelContext $context): ?ProductEntity
    {
        $criteria = new Criteria([$productId]);
        $criteria->addAssociation('manufacturer');
        return $this->productRepository->search($criteria, $context)->first();
    }
}
