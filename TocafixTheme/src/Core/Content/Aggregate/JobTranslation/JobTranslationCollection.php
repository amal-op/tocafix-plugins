<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\JobTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class JobTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return JobTranslationEntity::class;
    }
}