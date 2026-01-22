<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\TeamCategory;

use TocafixTheme\Core\Content\Aggregate\TeamCategoryRelation\TeamCategoryRelationDefinition;
use TocafixTheme\Core\Content\Aggregate\TeamCategoryTranslation\TeamCategoryTranslationDefinition;
use TocafixTheme\Core\Content\Team\TeamDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\VersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class TeamCategoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = "tocafix_team_category";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;    
    }

    public function getCollectionClass(): string
    {
        return TeamCategoryCollection::class;
    }

    public function getEntityClass(): string
    {
        return TeamCategoryEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new VersionField()),
            new TranslatedField('name'),
            (new BoolField('active', 'active')),
            new CreatedAtField(),
            new UpdatedAtField(),
            (new TranslationsAssociationField(TeamCategoryTranslationDefinition::class, 'tocafix_team_category_id'))->addFlags(new Required()),
            new ManyToManyAssociationField('teams', TeamDefinition::class, TeamCategoryRelationDefinition::class, 'tocafix_team_category_id', 'tocafix_team_id')
        ]);
    }
}