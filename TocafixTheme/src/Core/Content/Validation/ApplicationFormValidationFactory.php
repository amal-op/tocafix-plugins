<?php

declare (strict_types=1);
namespace TocafixTheme\Core\Content\Validation;

use Shopware\Core\Framework\Validation\BuildValidationEvent;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\Framework\Validation\DataValidationDefinition;
use Shopware\Core\Framework\Validation\DataValidationFactoryInterface;
use Shopware\Core\System\Annotation\Concept\ExtensionPattern\Decoratable;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class ApplicationFormValidationFactory
 *
 * This class is responsible for generating and managing validation definitions for application forms within different sales channel contexts. 
 * It offers methods for creating and updating DataValidationDefinitions tailored for application forms, ensuring robust data validation and 
 * customization through event dispatching mechanisms.
 * 
 * The class leverages an EventDispatcherInterface for dispatching events that allow further customization of the validation definitions and 
 * a SystemConfigService for accessing necessary system configuration settings.
 * 
 * @Decoratable
 */
class ApplicationFormValidationFactory implements DataValidationFactoryInterface
{
    
    /**
     * The event dispatcher used to handle and dispatch events within the application.
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    /**
     * The service used for managing and retrieving system configuration settings.
     *
     * @var SystemConfigService
     */
    private $systemConfigService;
    
    /**
     * Constructor method for initializing the class with necessary dependencies.
     *
     * This method accepts two dependencies, an EventDispatcherInterface and a SystemConfigService,
     * which are essential for the class's operation. The EventDispatcherInterface is used for dispatching
     * events, and the SystemConfigService is used for accessing system configuration settings.
     *
     * @param EventDispatcherInterface $eventDispatcher The event dispatcher service for managing events.
     * @param SystemConfigService $systemConfigService The system configuration service for accessing config settings.
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, SystemConfigService $systemConfigService)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->systemConfigService = $systemConfigService;
    }
    
    /**
     * Creates a DataValidationDefinition for the application form within the given sales channel context.
     * 
     * This method utilizes the 'createApplicationFormValidation' method to generate a validation definition
     * specifically for the 'application_form.create' context within the provided sales channel context.
     *
     * @param SalesChannelContext $context The sales channel context in which the validation definition is created.
     * 
     * @return DataValidationDefinition The generated data validation definition for the application form.
     */
    public function create(SalesChannelContext $context): DataValidationDefinition
    {
        return $this->createApplicationFormValidation('application_form.create', $context);
    }
    
    /**
     * Updates the application form validation definition for the given sales channel context.
     *
     * This method creates and returns a DataValidationDefinition object configured specifically for updating 
     * application forms within the provided SalesChannelContext.
     *
     * @param SalesChannelContext $context The context of the sales channel in which the application form update is to be validated.
     * @return DataValidationDefinition The validation definition for updating the application form.
     */
    public function update(SalesChannelContext $context): DataValidationDefinition
    {
        return $this->createApplicationFormValidation('application_form.update', $context);
    }
    
    /**
     * Creates and returns a DataValidationDefinition for an application form.
     *
     * This method sets up a validation definition with various required fields and 
     * dispatches a BuildValidationEvent to allow further customization.
     *
     * @param string $validationName The name of the validation definition.
     * @param SalesChannelContext $context The sales channel context containing contextual information.
     * @return DataValidationDefinition The constructed DataValidationDefinition object.
     */
    private function createApplicationFormValidation(string $validationName, SalesChannelContext $context): DataValidationDefinition
    {
        $definition = new DataValidationDefinition($validationName);
        $definition->add('email', new NotBlank(), new Email())->add('street', new NotBlank())->add('plz_ort', new NotBlank())->add('firstName', new NotBlank())->add('lastName', new NotBlank())->add('phone', new NotBlank());
        $validationEvent = new BuildValidationEvent($definition, new DataBag(), $context->getContext());
        $this->eventDispatcher->dispatch($validationEvent, $validationEvent->getName());
        return $definition;
    }
}