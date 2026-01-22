<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\TeamTranslation;

use TocafixTheme\Core\Content\Team\TeamDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class TeamTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'tocafix_team_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return TeamTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return TeamTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return TeamDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required),
            new StringField('position', 'position')
        ]);
    }
}