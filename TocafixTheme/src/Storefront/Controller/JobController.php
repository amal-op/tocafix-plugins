<?php

declare(strict_types=1);

namespace TocafixTheme\Storefront\Controller;

use TocafixTheme\Storefront\Page\Job\JobPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

/**
 * Class JobController
 *
 * This class is responsible for managing the job-related pages in the storefront.
 * It offers methods for displaying job details, ensuring that job pages are correctly fetched and rendered
 * based on the provided job ID. If a job is not found, it handles rendering a 404 error template.
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
class JobController extends StorefrontController
{
    /**
     * Constructor for initializing the JobPageLoader.
     *
     * @param JobPageLoader $jobPageLoader An instance of JobPageLoader to manage job page loading.
     */
    public function __construct(
        private readonly JobPageLoader $jobPageLoader
    ) {
    }
    
    /**
     * Handles the display of a job detail page.
     *
     * This method processes the request to fetch and render the job detail page. If the job is found,
     * it renders the job detail template. If the job is not found, it renders a 404 error template.
     *
     * @param Request $request The HTTP request object containing the job ID.
     * @param SalesChannelContext $context The sales channel context for the current request.
     * @return Response The response object containing the rendered template.
     */
    #[Route(
        path: '/job/{id}',
        name: 'frontend.detail.job',
        methods: ['GET'],
        defaults: ['_noStore' => true]
    )]
    public function jobDetail(Request $request, SalesChannelContext $context): Response
    {
        $jobPage = $this->jobPageLoader->load($request, $context);
        $job = $jobPage->getJob();
        
        if ($job) {
            return $this->renderStorefront(
                '@Storefront/storefront/page/job/index.html.twig',
                ['page' => $jobPage]
            );
        }
        
        return $this->renderStorefront(
            '@Storefront/storefront/page/error/error-404.html.twig',
            ['page' => $jobPage]
        );
    }
}