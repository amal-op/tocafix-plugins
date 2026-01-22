<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\TeamCategoryRelation;

use TocafixTheme\Core\Content\Team\TeamDefinition;
use TocafixTheme\Core\Content\TeamCategory\TeamCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

class TeamCategoryRelationDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'tocafix_team_category_relation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('tocafix_team_id', 'teamId', TeamDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('tocafix_team_category_id', 'categoryId', TeamCategoryDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new ReferenceVersionField(TeamCategoryDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('team', 'tocafix_team_id', TeamDefinition::class),
            new ManyToOneAssociationField('teamCategory', 'tocafix_team_category_id', TeamCategoryDefinition::class),
            new CreatedAtField(),
        ]);
    }
}
