<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Page\Job;

use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JobPageLoader
 *
 * This class is responsible for handling the loading and rendering of job-related pages.
 * It offers methods for initializing necessary dependencies, loading job data, and dispatching events,
 * ensuring efficient and organized management of job page operations.
 * 
 * The class guarantees seamless integration with a generic page loader, an event dispatcher, and a job repository,
 * facilitating a modular and scalable approach to job page management.
 * 
 * @param GenericPageLoaderInterface $genericLoader The generic page loader instance.
 * @param EventDispatcherInterface $eventDispatcher The event dispatcher instance.
 * @param EntityRepository $jobRepository The job repository instance.
 */
class JobPageLoader
{
    
    /**
     * The generic page loader used for loading pages.
     *
     * @var GenericPageLoaderInterface
     */
    private GenericPageLoaderInterface $genericLoader;
    
    /**
     * The event dispatcher instance used to handle and dispatch events.
     *
     * @var EventDispatcherInterface
     */
    private EventDispatcherInterface $eventDispatcher;
    
    /**
     * Repository for handling job entities.
     *
     * @var EntityRepository
     */
    private EntityRepository $jobRepository;
    
    /**
     * Constructor for initializing the necessary dependencies for the class.
     * 
     * This constructor sets up the generic page loader, event dispatcher, and job repository
     * which are essential for the class operations.
     *
     * @param GenericPageLoaderInterface $genericLoader The generic page loader instance.
     * @param EventDispatcherInterface $eventDispatcher The event dispatcher instance.
     * @param EntityRepository $jobRepository The job repository instance.
     */
    public function __construct(GenericPageLoaderInterface $genericLoader, EventDispatcherInterface $eventDispatcher, EntityRepository $jobRepository)
    {
        $this->genericLoader = $genericLoader;
        $this->eventDispatcher = $eventDispatcher;
        $this->jobRepository = $jobRepository;
    }
    
    public function load(Request $request, SalesChannelContext $context): JobPage
    {
        $id = $request->attributes->get('id');
        if (!$id) {
            throw new MissingRequestParameterException('id', '/id');
        }
        $jobInfo = null;
        $salesChannelContext = $context->getContext();
        $criteria = new Criteria([$id]);
        $criteria->addAssociation('media');
        $jobInfo = $this->jobRepository->search($criteria, $salesChannelContext)->first();
        $page = $this->genericLoader->load($request, $context);
        $page = JobPage::createFrom($page);
        $page->setJob($jobInfo);
        $this->eventDispatcher->dispatch(new JobPageLoadedEvent($page, $context, $request));
        return $page;
    }
}