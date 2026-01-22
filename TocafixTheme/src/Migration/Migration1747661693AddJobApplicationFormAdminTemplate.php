<?php declare(strict_types=1);

namespace TocafixTheme\Migration;

use Doctrine\DBAL\Connection;
use Exception;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;
use TocafixTheme\Setup\Assets\JobApplicationFormAdminEmailTemplate;

class Migration1747661693AddJobApplicationFormAdminTemplate extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1747661693;
    }

    public function update(Connection $connection): void
    {
        $mailTemplateTypeId = $this->createMailTemplateType($connection);
        $mailTemplateId = $this->createAdminMailTemplate($connection, $mailTemplateTypeId);
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }

    private function createMailTemplateType(Connection $connection): string
    {
        $technicalName = 'job_application_mail_template_type';
        
        $existingId = $connection->fetchOne(
            'SELECT LOWER(HEX(id)) FROM mail_template_type WHERE technical_name = :technicalName',
            ['technicalName' => $technicalName]
        );

        if ($existingId) {
            $mailTemplateTypeId = $existingId;
            
            $connection->update('mail_template_type', [
                'updated_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ], [
                'id' => Uuid::fromHexToBytes($mailTemplateTypeId)
            ]);
        } else {
            $mailTemplateTypeId = Uuid::randomHex();
            $connection->insert('mail_template_type', [
                'id' => Uuid::fromHexToBytes($mailTemplateTypeId),
                'technical_name' => $technicalName,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);
        }
        
        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');
        $frLangId = $this->getLanguageIdByLocale($connection, 'fr-FR');
        
        $englishName = 'Job Application';
        $germanName = 'Bewerbung';
        $frenchName = "Demande d'emploi";
        
        if ($defaultLangId !== $deLangId) {
            $this->upsertMailTemplateTypeTranslation($connection, $mailTemplateTypeId, $defaultLangId, $englishName);
        }
        
        if ($defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            $this->upsertMailTemplateTypeTranslation($connection, $mailTemplateTypeId, Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM), $englishName);
        }
        
        if ($deLangId) {
            $this->upsertMailTemplateTypeTranslation($connection, $mailTemplateTypeId, $deLangId, $germanName);
        }
        
        if ($frLangId) {
            $this->upsertMailTemplateTypeTranslation($connection, $mailTemplateTypeId, $frLangId, $frenchName);
        }
        
        return $mailTemplateTypeId;
    }

    private function upsertMailTemplateTypeTranslation(Connection $connection, string $mailTemplateTypeId, $languageId, string $name): void
    {
        $exists = $connection->fetchOne(
            'SELECT 1 FROM mail_template_type_translation 
             WHERE mail_template_type_id = :templateId AND language_id = :langId',
            [
                'templateId' => Uuid::fromHexToBytes($mailTemplateTypeId),
                'langId' => $languageId
            ]
        );
        
        if ($exists) {
            $connection->update('mail_template_type_translation', [
                'name' => $name,
                'updated_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ], [
                'mail_template_type_id' => Uuid::fromHexToBytes($mailTemplateTypeId),
                'language_id' => $languageId
            ]);
        } else {
            try {
                $connection->insert('mail_template_type_translation', [
                    'mail_template_type_id' => Uuid::fromHexToBytes($mailTemplateTypeId),
                    'language_id' => $languageId,
                    'name' => $name,
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
                ]);
            } catch (Exception $e) {
            }
        }
    }

    private function getLanguageIdByLocale(Connection $connection, string $locale): ?string
    {
        $sql = <<<SQL
        SELECT `language`.`id`
        FROM `language`
        INNER JOIN `locale` ON `locale`.`id` = `language`.`locale_id`
        WHERE `locale`.`code` = :code
        SQL;
        $languageId = $connection->executeQuery($sql, ['code' => $locale])->fetchOne();
        if (!$languageId && $locale !== 'en-GB') {
            return null;
        }
        if (!$languageId) {
            return Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM);
        }
        return $languageId;
    }

    private function createAdminMailTemplate(Connection $connection, string $mailTemplateTypeId): string
    {
        $existingTemplateId = $connection->fetchOne(
            'SELECT LOWER(HEX(id)) FROM mail_template WHERE mail_template_type_id = :typeId',
            ['typeId' => Uuid::fromHexToBytes($mailTemplateTypeId)]
        );

        if ($existingTemplateId) {
            $mailTemplateId = $existingTemplateId;
            
            $connection->update('mail_template', [
                'updated_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ], [
                'id' => Uuid::fromHexToBytes($mailTemplateId)
            ]);
        } else {
            $mailTemplateId = Uuid::randomHex();
            $connection->insert('mail_template', [
                'id' => Uuid::fromHexToBytes($mailTemplateId),
                'mail_template_type_id' => Uuid::fromHexToBytes($mailTemplateTypeId),
                'system_default' => 0,
                'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);
        }
        
        $defaultLangId = $this->getLanguageIdByLocale($connection, 'en-GB');
        $deLangId = $this->getLanguageIdByLocale($connection, 'de-DE');
        $frLangId = $this->getLanguageIdByLocale($connection, 'fr-FR');
        
        $applicationFormAdminTemplate = new JobApplicationFormAdminEmailTemplate();
        
        if ($defaultLangId !== $deLangId) {
            $this->upsertMailTemplateTranslation($connection, $mailTemplateId, $defaultLangId, [
                'sender_name' => 'Allega',
                'subject' => 'Job Application Form',
                'description' => 'Job Application Form Submission To Admin',
                'content_html' => $applicationFormAdminTemplate->getContentHtmlEn(),
                'content_plain' => $applicationFormAdminTemplate->getContentPlainEn()
            ]);
        }
        
        if ($defaultLangId !== Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM)) {
            $this->upsertMailTemplateTranslation($connection, $mailTemplateId, Uuid::fromHexToBytes(Defaults::LANGUAGE_SYSTEM), [
                'sender_name' => 'Allega',
                'subject' => 'Job Application Form',
                'description' => 'Job Application Form Submission To Admin',
                'content_html' => $applicationFormAdminTemplate->getContentHtmlEn(),
                'content_plain' => $applicationFormAdminTemplate->getContentPlainEn()
            ]);
        }
        
        if ($deLangId) {
            $this->upsertMailTemplateTranslation($connection, $mailTemplateId, $deLangId, [
                'sender_name' => 'Allega',
                'subject' => 'Bewerbungsformular',
                'description' => 'Einreichung des Bewerbungsformulars bei Admin',
                'content_html' => $applicationFormAdminTemplate->getContentHtmlDe(),
                'content_plain' => $applicationFormAdminTemplate->getContentPlainDe()
            ]);
        }
        
        if ($frLangId) {
            $this->upsertMailTemplateTranslation($connection, $mailTemplateId, $frLangId, [
                'sender_name' => 'Allega',
                'subject' => "Formulaire de demande d'emploi",
                'description' => "Soumission du formulaire de demande d'emploi à l'administrateur",
                'content_html' => $applicationFormAdminTemplate->getContentHtmlFr(),
                'content_plain' => $applicationFormAdminTemplate->getContentPlainFr()
            ]);
        }
        
        return $mailTemplateId;
    }

    private function upsertMailTemplateTranslation(Connection $connection, string $mailTemplateId, $languageId, array $data): void
    {
        $exists = $connection->fetchOne(
            'SELECT 1 FROM mail_template_translation 
             WHERE mail_template_id = :templateId AND language_id = :langId',
            [
                'templateId' => Uuid::fromHexToBytes($mailTemplateId),
                'langId' => $languageId
            ]
        );
        
        if ($exists) {
            $updateData = array_merge($data, [
                'updated_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
            ]);
            
            $connection->update('mail_template_translation', $updateData, [
                'mail_template_id' => Uuid::fromHexToBytes($mailTemplateId),
                'language_id' => $languageId
            ]);
        } else {
            try {
                $insertData = array_merge([
                    'mail_template_id' => Uuid::fromHexToBytes($mailTemplateId),
                    'language_id' => $languageId
                ], $data, [
                    'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT)
                ]);
                
                $connection->insert('mail_template_translation', $insertData);
            } catch (Exception $e) {
            }
        }
    }
}