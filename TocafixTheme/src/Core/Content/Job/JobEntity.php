<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Job;

use TocafixTheme\Core\Content\Aggregate\JobTranslation\JobTranslationCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class JobEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $shortDescriptionTeaser;

    /**
     * @var string|null
     */
    protected $shortDescriptionDetail;

    /**
     * @var string|null
     */
    protected $description;

    /**
     * @var string
     */
    protected $jobDate;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string|null
     */
    protected $mediaId;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    /**
     * @var JobTranslationCollection|null
     */
    protected $translations;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getShortDescriptionTeaser(): ?string
    {
        return $this->shortDescriptionTeaser;
    }

    public function setShortDescriptionTeaser(?string $shortDescriptionTeaser): void
    {
        $this->shortDescriptionTeaser = $shortDescriptionTeaser;
    }

    public function getShortDescriptionDetail(): ?string
    {
        return $this->shortDescriptionDetail;
    }

    public function setShortDescriptionDetail(?string $shortDescriptionDetail): void
    {
        $this->shortDescriptionDetail = $shortDescriptionDetail;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getJobDate(): string
    {
        return $this->jobDate;
    }

    public function setJobDate(string $jobDate): void
    {
        $this->jobDate = $jobDate;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function setMediaId(?string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getTranslations(): ?JobTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(JobTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }
}
