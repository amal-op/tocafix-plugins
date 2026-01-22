<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\TeamCategoryTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class TeamCategoryTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return TeamCategoryTranslationEntity::class;
    }
}