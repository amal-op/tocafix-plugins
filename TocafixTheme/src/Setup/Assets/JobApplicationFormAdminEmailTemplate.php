<?php

declare (strict_types=1);
namespace TocafixTheme\Setup\Assets;

/**
 * Class JobApplicationFormAdminEmailTemplate
 *
 * This class is responsible for generating email content templates in multiple languages (English, German, and French) for job application forms. 
 * It offers methods for creating both HTML and plain text versions of email contents, ensuring that placeholders are properly replaced with actual 
 * application form data before sending the email.
 * 
 * The key operations provided by this class include:
 * - Generating HTML email content in English, German, and French.
 * - Generating plain text email content in English, German, and French.
 * 
 * These functionalities ensure that application form submissions can be communicated effectively in different languages, accommodating diverse user preferences.
 */
class JobApplicationFormAdminEmailTemplate
{
    
    /**
     * Generates and returns the HTML content for an email in English.
     *
     * This method constructs a templated HTML string, filling in data from the 
     * job application form. The placeholders should be replaced with actual 
     * application form data before sending the email.
     *
     * @return string The HTML content for the email.
     */
    public function getContentHtmlEn()
    {
        return <<<MAIL
        <div style="font-family:arial; font-size:12px;">
            <p>
                The following Message was sent to you via the job application form.<br/>
                <br/>
                Job Title: {{ applicationFormData.jobTitle }}
                <br/>
                Name: {{ applicationFormData.gender }} {{ applicationFormData.firstName }} {{ applicationFormData.lastName }}
                <br/>
                Address: {{ applicationFormData.street }} {{ applicationFormData.plz_ort }}
                <br/>
                Email: {{ applicationFormData.email }}
                <br/>
                Phone: {{ applicationFormData.phone }}<br/>
                <br/>
                Message:<br/>
                {{ applicationFormData.comment|nl2br }}<br/>
            </p>
        </div>
        MAIL;
    }
    
    /**
     * Generates a plain text email content using the application form data.
     *
     * This method constructs a plain text email message that includes various details 
     * from the application form data such as job title, contact name, address, email, 
     * phone number, and a message.
     *
     * @return string The generated plain text email content.
     */
    public function getContentPlainEn()
    {
        return <<<MAIL
        The following Message was sent to you via the application form.
        Job Title: {{ applicationFormData.jobTitle }}
        Contact name: {{ applicationFormData.gender }} {{ applicationFormData.firstName }} {{ applicationFormData.lastName }}
        
        Address: {{ applicationFormData.street }} {{ applicationFormData.plz_ort }}
        
        Email: {{ applicationFormData.email }}
        
        Phone: {{ applicationFormData.phone }}
        
        Message:
        {{ applicationFormData.comment }}
        MAIL;
    }
    
    /**
     * Generates and returns the HTML content for a German application form submission.
     *
     * The HTML content includes information such as job title, contact name, address, email, phone number, and a message.
     *
     * @return string The HTML content for the application form submission in German.
     */
    public function getContentHtmlDe()
    {
        return <<<MAIL
        <div style="font-family:arial; font-size:12px;">
            <p>
                Die folgende Nachricht wurde Ihnen über das Antragsformular gesendet.<br/>
                <br/>
                Berufsbezeichnung: {{ applicationFormData.jobTitle }}
                <br/>
                Kontaktname: {{ applicationFormData.gender }} {{ applicationFormData.firstName }} {{ applicationFormData.lastName }}
                <br/>
                Adresse: {{ applicationFormData.street }} {{ applicationFormData.plz_ort }}
                <br/>
                E-Mail: {{ applicationFormData.email }}
                <br/>
                Telefon: {{ applicationFormData.phone }}<br/>
                <br/>
                Nachricht:<br/>
                {{ applicationFormData.comment|nl2br }}<br/>
            </p>
        </div>
        MAIL;
    }
    
    /**
     * Generates and returns a plain text email content template in German.
     *
     * The returned template includes placeholders for application form data such as job title, contact name,
     * address, email, phone number, and a message. These placeholders should be replaced with actual data
     * when generating the final email content.
     *
     * @return string The plain text email content template in German.
     */
    public function getContentPlainDe()
    {
        return <<<MAIL
        Die folgende Nachricht wurde Ihnen über das Antragsformular gesendet.
        
        Berufsbezeichnung: {{ applicationFormData.jobTitle }}
                
        Kontaktname: {{ applicationFormData.gender }} {{ applicationFormData.firstName }} {{ applicationFormData.lastName }}
        
        Adresse: {{ applicationFormData.street }} {{ applicationFormData.plz_ort }}
        
        E-Mail: {{ applicationFormData.email }}
        
        Telefon: {{ applicationFormData.phone }}
        
        Nachricht:
        {{ applicationFormData.comment }}
        MAIL;
    }
    
    /**
     * Generates an HTML string for the content of an email in French.
     *
     * This method prepares the HTML content for an email, utilizing data from an application form.
     *
     * @return string The HTML content for the email.
     */
    public function getContentHtmlFr(): string
    {
        return <<<MAIL
        <div style="font-family:arial; font-size:12px;">
            <p>
                Le message suivant vous a été envoyé via le formulaire de candidature.<br/>
                <br/>
                Titre d'emploi: {{ applicationFormData.jobTitle }}
                <br/>
                Nom du contact: {{ applicationFormData.gender }} {{ applicationFormData.firstName }} {{ applicationFormData.lastName }}
                <br/>
                Adresse: {{ applicationFormData.street }} {{ applicationFormData.plz_ort }}
                <br/>
                E-mail: {{ applicationFormData.email }}
                <br/>
                Téléphoner: {{ applicationFormData.phone }}<br/>
                <br/>
                Message:<br/>
                {{ applicationFormData.comment|nl2br }}<br/>
            </p>
        </div>
        MAIL;
    }
    
    /**
     * Generates a plain text email content in French based on the application form data.
     *
     * The method returns a string template for an email message that includes various details such as 
     * the job title, contact name, address, email, phone number, and a comment. The placeholders in 
     * the template are expected to be replaced with actual form data.
     *
     * @return string The plain text email content in French.
     */
    public function getContentPlainFr(): string
    {
        return <<<MAIL
                Le message suivant vous a été envoyé via le formulaire de candidature.
        
                Titre d'emploi: {{ applicationFormData.jobTitle }}
                
                Nom du contact: {{ applicationFormData.gender }} {{ applicationFormData.firstName }} {{ applicationFormData.lastName }}
                
                Adresse: {{ applicationFormData.street }} {{ applicationFormData.plz_ort }}
                
                E-mail: {{ applicationFormData.email }}
                
                Téléphoner: {{ applicationFormData.phone }}
                
                Message:
                {{ applicationFormData.comment }}
        MAIL;
    }
}