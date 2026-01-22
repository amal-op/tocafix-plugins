<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\TeamCategory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class TeamCategoryCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return TeamCategoryEntity::class;
    }
}