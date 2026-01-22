<?php

declare(strict_types=1);

namespace ShopwareBundlePlugin\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class ShopwareBundleController extends StorefrontController
{
    /**
     * @var EntityRepository
     */
    private $productRepository;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    #[Route(path: '/recently-viewed-product-list', name: 'frontend.recently-viewed-product.list', methods: ['POST'], defaults: ['XmlHttpRequest' => true])]
    public function loadProductList(Request $request, SalesChannelContext $context): Response
    {
        $productIds = $request->get('productIds');
        $sidebar = $request->get('sidebar');
        $layoutColumns = $request->get('layoutColumns');
        $boxLayout = $request->get('boxLayout');
        $displayMode = $request->get('displayMode');
        $productsPerPage = $request->get('productsPerPage');

        if (empty($productIds)) {
            return new Response();
        }

        $criteria = new Criteria($productIds);
        $criteria->addAssociation('cover');
        $criteria->setLimit($productsPerPage);
        
        $products = $this->productRepository->search($criteria, $context->getContext());

        return $this->renderStorefront('@Storefront/storefront/component/cmsbundle-recently-viewed-product/listing.html.twig', [
            'searchResult' => $products,
            'sidebar' => $sidebar,
            'layoutColumns' => $layoutColumns,
            'boxLayout' => $boxLayout,
            'displayMode' => $displayMode,
        ]);
    }

    #[Route(path: '/recently-viewed-product-slider', name: 'frontend.recently-viewed-product.slider', methods: ['POST'], defaults: ['XmlHttpRequest' => true])]
    public function loadProductSLider(Request $request, SalesChannelContext $context): Response
    {
        $productIds = $request->get('productIds');
        $boxLayout = $request->get('boxLayout');
        $displayMode = $request->get('displayMode');

        if (empty($productIds)) {
            return new Response();
        }

        $criteria = new Criteria($productIds);
        $criteria->addAssociation('cover');

        $products = $this->productRepository->search($criteria, $context->getContext());

        return $this->renderStorefront('@Storefront/storefront/component/cmsbundle-recently-viewed-product/slider.html.twig', [
            'products' => $products,
            'boxLayout' => $boxLayout,
            'displayMode' => $displayMode,
        ]);
    }
}
