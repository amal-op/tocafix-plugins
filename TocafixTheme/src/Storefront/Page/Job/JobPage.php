<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Page\Job;

use TocafixTheme\Core\Content\Job\JobEntity;
use Shopware\Storefront\Page\Page;

/**
 * Class JobPage
 *
 * This class is responsible for managing job details, specifically handling the assignment and retrieval of job entities.
 * It offers methods for setting and getting the current job entity, ensuring that job details can be dynamically managed within the instance.
 * Users of this class can assign a job entity to an instance or retrieve the currently assigned job entity, with the flexibility of having no job entity assigned if needed.
 * 
 * Class for set and get the Job detail.
 */
class JobPage extends Page
{
    
    protected $job;
    
    /**
     * Retrieves the current job entity.
     *
     * This method returns the current JobEntity instance associated with the object.
     * If no job entity is set, it will return null.
     *
     * @return JobEntity|null The current JobEntity instance or null if none is set.
     */
    public function getJob(): ?JobEntity
    {
        return $this->job;
    }
    
    /**
     * Sets the job entity for the current instance.
     *
     * This method allows you to assign a job entity to the current instance. 
     * The job entity can also be null, which indicates that there is no job assigned.
     *
     * @param JobEntity|null $job The job entity to assign, or null to unset the job.
     * @return void
     */
    public function setJob(?JobEntity $job): void
    {
        $this->job = $job;
    }
}