<?php declare(strict_types=1);

namespace TocafixTheme\Core\Content\Aggregate\JobTranslation;

use TocafixTheme\Core\Content\Job\JobDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class JobTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'tocafix_job_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return JobTranslationCollection::class;
    }

    public function getEntityClass(): string
    {
        return JobTranslationEntity::class;
    }

    public function getParentDefinitionClass(): string
    {
        return JobDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('name', 'name'))->addFlags(new Required),
            (new LongTextField('short_description_teaser', 'shortDescriptionTeaser'))->addFlags(new AllowHtml()),
            (new LongTextField('short_description_detail', 'shortDescriptionDetail'))->addFlags(new AllowHtml()),
            (new LongTextField('description', 'description'))->addFlags(new AllowHtml()),
        ]);
    }
}