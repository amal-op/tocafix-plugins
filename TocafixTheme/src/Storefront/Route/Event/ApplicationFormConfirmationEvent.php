<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Route\Event;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Event\EventData\EventDataCollection;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\Event\EventData\ObjectType;
use Shopware\Core\Framework\Event\FlowEventAware;
use Shopware\Core\Framework\Event\MailAware;
use Shopware\Core\Framework\Event\SalesChannelAware;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * Class ApplicationFormConfirmationEvent
 *
 * This class is responsible for handling the events related to the confirmation of application forms.
 * It offers methods for initializing the confirmation handler, retrieving event data, context, mail recipients, sales channel ID, and the application form confirmation data.
 * Ensuring the correct setup and retrieval of all relevant information, this class facilitates the processing and management of application form confirmations.
 * 
 * The class guarantees:
 * - Correct initialization of the event context and its dependencies.
 * - Structured retrieval of event-related data and context.
 * - Accurate access to recipient information and sales channel identifiers.
 * 

 */
final class ApplicationFormConfirmationEvent extends Event implements FlowEventAware, SalesChannelAware, MailAware
{
    public const EVENT_NAME = 'application_form_confirmation.send';
    
    /**
     * The context in which the current process or operation is executed.
     *
     * @var Context
     */
    private $context;
    
    /**
     * The unique identifier for the sales channel.
     *
     * @var string
     */
    private $salesChannelId;
    
    /**
     * The collection of mail recipients.
     *
     * @var MailRecipientStruct
     */
    private $recipients;
    
    /**
     * Stores the confirmation data for the application form.
     *
     * @var array
     */
    private $applicationFormConfirmationData;
    
    /**
     * Constructor for initializing the application form confirmation handler.
     *
     * This method sets up the context, sales channel ID, recipients, and the application form confirmation data.
     *
     * @param Context $context The context in which the application is running.
     * @param string $salesChannelId The ID of the sales channel.
     * @param MailRecipientStruct $recipients The recipients of the mail.
     * @param DataBag $applicationFormConfirmationData The data related to the application form confirmation.
     *
     * @return void
     */
    public function __construct(Context $context, string $salesChannelId, MailRecipientStruct $recipients, DataBag $applicationFormConfirmationData)
    {
        $this->context = $context;
        $this->salesChannelId = $salesChannelId;
        $this->recipients = $recipients;
        $this->applicationFormConfirmationData = $applicationFormConfirmationData->all();
    }
    
    /**
     * Retrieves an EventDataCollection containing available data.
     *
     * This method creates a new EventDataCollection instance and adds 
     * an 'applicationFormConfirmationData' entry with an ObjectType.
     *
     * @return EventDataCollection The collection of available event data.
     */
    public static function getAvailableData(): EventDataCollection
    {
        return (new EventDataCollection())->add('applicationFormConfirmationData', new ObjectType());
    }
    
    /**
     * Retrieves the name of the event.
     *
     * @return string The name of the event.
     */
    public function getName(): string
    {
        return self::EVENT_NAME;
    }
    
    /**
     * Retrieves the current context.
     *
     * This method returns the configured context object. The context
     * encapsulates environment-specific information and settings.
     *
     * @return Context The current context instance.
     */
    public function getContext(): Context
    {
        return $this->context;
    }
    
    /**
     * Retrieves the mail structure for the recipients.
     *
     * This method returns a MailRecipientStruct object which contains
     * the structured information of the mail recipients.
     *
     * @return MailRecipientStruct The structured information of the mail recipients.
     */
    public function getMailStruct(): MailRecipientStruct
    {
        return $this->recipients;
    }
    
    /**
     * Retrieves the sales channel identifier.
     *
     * This method returns the unique identifier for the sales channel associated with the current instance.
     *
     * @return string The unique identifier of the sales channel.
     */
    public function getSalesChannelId(): string
    {
        return $this->salesChannelId;
    }
    
    /**
     * Retrieves the application form confirmation data.
     *
     * This method returns an array containing the confirmation data
     * associated with the application form.
     *
     * @return array The application form confirmation data.
     */
    public function getApplicationFormConfirmationData(): array
    {
        return $this->applicationFormConfirmationData;
    }
}