<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Team;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class TeamCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return TeamEntity::class;
    }
}