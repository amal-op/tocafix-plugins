<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\TeamTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class TeamTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return TeamTranslationEntity::class;
    }
}