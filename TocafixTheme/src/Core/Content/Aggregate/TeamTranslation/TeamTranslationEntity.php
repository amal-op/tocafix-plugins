<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\TeamTranslation;

use TocafixTheme\Core\Content\Team\TeamEntity;
use Shopware\Core\Framework\DataAbstractionLayer\TranslationEntity;

class TeamTranslationEntity extends TranslationEntity
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
     * @var string
     */
    protected $position;

    /**
     * @var TeamEntity
     */
    protected $team;

    public function getTeamId(): string
    {
        return $this->teamId;
    }

    public function setTeamId(string $teamId): void
    {
        $this->teamId = $teamId;
    }

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

    public function getTeam(): ?TeamEntity
    {
        return $this->team;
    }

    public function setTeam(TeamEntity $team): void
    {
        $this->team = $team;
    }
}