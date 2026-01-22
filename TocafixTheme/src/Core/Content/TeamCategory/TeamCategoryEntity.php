<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\TeamCategory;

use TocafixTheme\Core\Content\Aggregate\TeamCategoryTranslation\TeamCategoryTranslationCollection;
use TocafixTheme\Core\Content\Team\TeamCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class TeamCategoryEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var bool
     */
    protected $active;

    /**
     * @var TeamCategoryTranslationCollection|null
     */
    protected $translations;

    /**
     * @var TeamCollection|null
     */
    protected $teams;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    public function setPosition(int $position): void
    {
        $this->position = $position;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getTranslations(): ?TeamCategoryTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(TeamCategoryTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getTeams(): ?TeamCollection
    {
        return $this->teams;
    }

    public function setTeams(?TeamCollection $teams): void
    {
        $this->teams = $teams;
    }
}