<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\TeamCategoryTranslation;

use TocafixTheme\Core\Content\TeamCategory\TeamCategoryEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class TeamCategoryTranslationEntity extends TranslationEntity
{
    /**
     * @var string
     */
    protected $teamId;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var TeamCategoryEntity
     */
    protected $teamCategory;

    public function getTeamCategoryId(): string
    {
        return $this->teamCategoryId;
    }

    public function setTeamCategoryId(string $teamCategoryId): void
    {
        $this->teamCategoryId = $teamCategoryId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getTeamCategory(): ?TeamCategoryEntity
    {
        return $this->teamCategory;
    }

    public function setTeamCategory(TeamCategoryEntity $teamCategory): void
    {
        $this->teamCategory = $teamCategory;
    }
}