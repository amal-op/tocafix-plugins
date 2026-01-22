<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\JobTranslation;

use TocafixTheme\Core\Content\Job\JobEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class JobTranslationEntity extends TranslationEntity
{
    /**
     * @var string
     */
    protected $jobId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var JobEntity
     */
    protected $job;

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function setJobId(string $jobId): void
    {
        $this->jobId = $jobId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getShortDescriptionTeaser(): string
    {
        return $this->shortDescriptionTeaser;
    }

    public function setShortDescriptionTeaser(string $shortDescriptionTeaser): void
    {
        $this->shortDescriptionTeaser = $shortDescriptionTeaser;
    }

    public function getShortDescriptionDetail(): string
    {
        return $this->shortDescriptionDetail;
    }

    public function setShortDescriptionDetail(string $shortDescriptionDetail): void
    {
        $this->shortDescriptionDetail = $shortDescriptionDetail;
    }

    public function getDescriptions(): string
    {
        return $this->description;
    }

    public function setDescriptions(string $description): void
    {
        $this->description = $description;
    }

    public function getJob(): ?JobEntity
    {
        return $this->job;
    }

    public function setJob(JobEntity $job): void
    {
        $this->job = $job;
    }
}