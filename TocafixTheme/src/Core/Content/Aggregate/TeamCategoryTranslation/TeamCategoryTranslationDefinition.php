<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\TeamCategoryTranslation;

use TocafixTheme\Core\Content\TeamCategory\TeamCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class TeamCategoryTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'tocafix_team_category_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return TeamCategoryTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return TeamCategoryTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return TeamCategoryDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required)
        ]);
    }
}