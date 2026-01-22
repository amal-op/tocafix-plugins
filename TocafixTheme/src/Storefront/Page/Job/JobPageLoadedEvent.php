<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Page\Job;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\PageLoadedEvent;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JobPageLoadedEvent
 *
 * This class is responsible for handling events related to the loading of a job page within a sales channel context.
 * It offers methods for initializing the job page with relevant context and request data, and for retrieving the current job page object,
 * ensuring that the job page is properly set up and accessible during its lifecycle.
 * 
 * @param JobPage $page The job page object to initialize.
 * @param SalesChannelContext $salesChannelContext The sales channel context for the request.
 * @param Request $request The HTTP request object.
 */
class JobPageLoadedEvent extends PageLoadedEvent
{
    
    /**
     * The JobPage instance associated with this class.
     *
     * @var JobPage
     */
    protected $page;
    
    /**
     * Constructor method for initializing the JobPageHandler class.
     * This method sets up the page object and calls the parent constructor
     * with the provided SalesChannelContext and Request objects.
     * 
     * @param JobPage $page The job page object to initialize.
     * @param SalesChannelContext $salesChannelContext The sales channel context for the request.
     * @param Request $request The HTTP request object.
     */
    public function __construct(JobPage $page, SalesChannelContext $salesChannelContext, Request $request)
    {
        $this->page = $page;
        parent::__construct($salesChannelContext, $request);
    }
    
    /**
     * Retrieves the current JobPage object.
     *
     * @return JobPage The current page object.
     */
    public function getPage(): JobPage
    {
        return $this->page;
    }
}