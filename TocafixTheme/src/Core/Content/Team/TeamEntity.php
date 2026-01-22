<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Team;

use TocafixTheme\Core\Content\Aggregate\TeamTranslation\TeamTranslationCollection;
use TocafixTheme\Core\Content\TeamCategory\TeamCategoryCollection;
use Shopware\Core\Content\Media\MediaEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class TeamEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * @var TeamCategoryCollection|null
     */
    protected $teamCategories;

    /**
     * @var string|null
     */
    protected $mediaId;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var string
     */
    protected $position;

    /**
     * @var int
     */
    protected $sortOrder;

    /**
     * @var TeamTranslationCollection|null
     */
    protected $translations;

    /**
     * @var MediaEntity|null
     */
    protected $media;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getMediaId(): ?string
    {
        return $this->mediaId;
    }

    public function setMediaId(?string $mediaId): void
    {
        $this->mediaId = $mediaId;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): void
    {
        $this->sortOrder = $sortOrder;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getTranslations(): ?TeamTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(TeamTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getTeamCategories(): ?TeamCategoryCollection
    {
        return $this->teamCategories;
    }

    public function setTeamCategories(?TeamCategoryCollection $teamCategories): void
    {
        $this->teamCategories = $teamCategories;
    }

    public function getMedia(): ?MediaEntity
    {
        return $this->media;
    }

    public function setMedia(?MediaEntity $media): void
    {
        $this->media = $media;
    }
}