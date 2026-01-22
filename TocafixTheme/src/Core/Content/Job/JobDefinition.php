<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Job;

use TocafixTheme\Core\Content\Aggregate\JobTranslation\JobTranslationDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class JobDefinition extends EntityDefinition
{
    public const ENTITY_NAME = "tocafix_job";

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;    
    }

    public function getCollectionClass(): string
    {
        return JobCollection::class;
    }

    public function getEntityClass(): string
    {
        return JobEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new TranslatedField('name'),
            new TranslatedField('shortDescriptionTeaser'),
            new TranslatedField('shortDescriptionDetail'),
            new TranslatedField('description'),
            (new DateField('job_date', 'jobDate'))->addFlags(new Required()),
            new FkField('media_id', 'mediaId', MediaDefinition::class),
            (new BoolField('active', 'active')),
            new CreatedAtField(),
            new UpdatedAtField(),
            (new TranslationsAssociationField(JobTranslationDefinition::class, 'tocafix_job_id'))->addFlags(new Required()),
            new ManyToOneAssociationField('media', 'media_id', MediaDefinition::class, 'id', true),
        ]);
    }
}
