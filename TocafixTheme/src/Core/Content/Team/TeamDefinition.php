<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Team;

use TocafixTheme\Core\Content\Aggregate\TeamCategoryRelation\TeamCategoryRelationDefinition;
use TocafixTheme\Core\Content\Aggregate\TeamTranslation\TeamTranslationDefinition;
use TocafixTheme\Core\Content\TeamCategory\TeamCategoryDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class TeamDefinition extends EntityDefinition
{
    public const ENTITY_NAME = "tocafix_team";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;    
    }

    public function getCollectionClass(): string
    {
        return TeamCollection::class;
    }

    public function getEntityClass(): string
    {
        return TeamEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('email', 'email'))->addFlags(new Required),
            (new StringField('phone_number', 'phoneNumber')),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            new TranslatedField('name'),
            new TranslatedField('position'),
            (new IntField('sort_order', 'sortOrder'))->addFlags(new Required),
            (new BoolField('active', 'active')),
            new CreatedAtField(),
            new UpdatedAtField(),
            (new TranslationsAssociationField(TeamTranslationDefinition::class, 'tocafix_team_id'))->addFlags(new Required()),
            (new ManyToManyAssociationField('teamCategories', TeamCategoryDefinition::class, TeamCategoryRelationDefinition::class, 'tocafix_team_id', 'tocafix_team_category_id')),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', true),
        ]);
    }
}