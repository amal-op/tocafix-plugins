<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Route;

use TocafixTheme\Storefront\Route\Event\ApplicationFormConfirmationEvent;
use Shopware\Core\Content\LandingPage\LandingPageDefinition;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Event\EventData\MailRecipientStruct;
use Shopware\Core\Framework\RateLimiter\RateLimiter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\DataValidationFactoryInterface;
use Shopware\Core\Framework\Validation\DataValidator;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Framework\Media\Exception\FileTypeNotAllowedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class ApplicationFormRoute
 *
 * This class is responsible for managing and processing application form data within a sales channel context.
 * It offers methods for validating form data, retrieving slot configurations, sending emails with specified templates,
 * and handling file attachments. These functionalities ensure reliable data validation, efficient email communication,
 * and secure file handling within the sales channel environment.
 * 
 * @RouteScope(scopes={"store-api"})
 */
class ApplicationFormRoute
{
    
    /**
     * Factory interface for creating data validation instances for application forms.
     *
     * @var DataValidationFactoryInterface
     */
    protected DataValidationFactoryInterface $applicationFormValidationFactory;
    
    /**
     * The validator used to check the correctness of data.
     *
     * @var DataValidator
     */
    protected DataValidator $validator;
    
    /**
     * The event dispatcher instance used to manage and dispatch events.
     *
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;
    
    /**
     * The service responsible for managing system configuration settings.
     *
     * @var SystemConfigService
     */
    protected SystemConfigService $systemConfigService;
    
    /**
     * Repository interface for CMS slot entities.
     *
     * @var EntityRepository
     */
    protected EntityRepository $cmsSlotRepository;
    
    /**
     * Repository interface for managing category entities.
     *
     * @var EntityRepository
     */
    protected EntityRepository $categoryRepository;
    
    /**
     * Repository for accessing and managing landing page entities.
     *
     * @var EntityRepository
     */
    protected EntityRepository $landingPageRepository;
    
    /**
     * Repository interface for accessing and managing product entities.
     *
     * @var EntityRepository
     */
    protected EntityRepository $productRepository;
    
    /**
     * The stack of HTTP request objects.
     *
     * @var RequestStack
     */
    protected RequestStack $requestStack;
    
    /**
     * The rate limiter instance used to control the frequency of certain actions.
     *
     * @var RateLimiter
     */
    protected RateLimiter $rateLimiter;
    
    /**
     * Repository for handling mail template types.
     *
     * @var EntityRepository
     */
    protected EntityRepository $mailTemplateTypeRepository;
    
    /**
     * The mail service used for sending emails.
     *
     * @var AbstractMailService
     */
    protected AbstractMailService $mailService;
    
    public function __construct(DataValidationFactoryInterface $applicationFormValidationFactory, DataValidator $validator, EventDispatcherInterface $eventDispatcher, SystemConfigService $systemConfigService, EntityRepository $cmsSlotRepository, EntityRepository $categoryRepository, EntityRepository $landingPageRepository, EntityRepository $productRepository, RequestStack $requestStack, RateLimiter $rateLimiter, EntityRepository $mailTemplateTypeRepository, AbstractMailService $mailService)
    {
        $this->applicationFormValidationFactory = $applicationFormValidationFactory;
        $this->validator = $validator;
        $this->eventDispatcher = $eventDispatcher;
        $this->systemConfigService = $systemConfigService;
        $this->cmsSlotRepository = $cmsSlotRepository;
        $this->categoryRepository = $categoryRepository;
        $this->landingPageRepository = $landingPageRepository;
        $this->productRepository = $productRepository;
        $this->requestStack = $requestStack;
        $this->rateLimiter = $rateLimiter;
        $this->mailTemplateTypeRepository = $mailTemplateTypeRepository;
        $this->mailService = $mailService;
    }
    
    public function load(RequestDataBag $data, SalesChannelContext $context): ApplicationFormRouteResponse
    {
        $this->validateApplicationForm($data, $context);
        if (($request = $this->requestStack->getMainRequest()) !== null && $request->getClientIp() !== null) {
            $this->rateLimiter->ensureAccepted(RateLimiter::CONTACT_FORM, $request->getClientIp());
        }
        $mailConfigs = $this->getMailConfigs($context, $data->get('slotId'), $data->get('navigationId'), $data->get('entityName'));
        if (empty($mailConfigs['receivers'])) {
            $mailConfigs['receivers'][] = $this->systemConfigService->get('core.basicInformation.email', $context->getSalesChannel()->getId());
        }
        $attachments = $this->prepareAttachments();
        $recipientStructs = [];
        foreach ($mailConfigs['receivers'] as $mail) {
            $recipientStructs[$mail] = $mail;
        }
        $this->sendMail($recipientStructs, $this->getMailTemplate($context->getContext()), $attachments, $context, $data);
        $customerEmail = [$data->get('email') => $data->get('firstName') . ' ' . $data->get('lastName')];
        $event = new ApplicationFormConfirmationEvent($context->getContext(), $context->getSalesChannel()->getId(), new MailRecipientStruct($customerEmail), $data);
        $this->eventDispatcher->dispatch($event, ApplicationFormConfirmationEvent::EVENT_NAME);
        $result = new ApplicationFormRouteResponseStruct();
        $result->assign(['individualSuccessMessage' => $mailConfigs['message'] ?? '']);
        return new ApplicationFormRouteResponse($result);
    }
    
    /**
     * Validates the application form data within the specified sales channel context.
     *
     * @param DataBag $data The data bag containing the application form data.
     * @param SalesChannelContext $context The sales channel context in which the validation is performed.
     *
     * @throws ConstraintViolationException If validation violations are found.
     *
     * @return void
     */
    protected function validateApplicationForm(DataBag $data, SalesChannelContext $context): void
    {
        $definition = $this->applicationFormValidationFactory->create($context);
        $violations = $this->validator->getViolations($data->all(), $definition);
        if ($violations->count() > 0) {
            throw new ConstraintViolationException($violations, $data->all());
        }
    }
    
    /**
     * Retrieves the slot configuration for a given slot ID, navigation ID, and context.
     * Depending on the entity name, it searches through different repositories and fetches
     * the corresponding entity to extract the slot configuration.
     *
     * @param string $slotId The identifier for the slot.
     * @param string $navigationId The identifier for the navigation.
     * @param SalesChannelContext $context The current sales channel context.
     * @param string|null $entityName The name of the entity (optional).
     *
     * @return array The slot configuration containing receivers and message information.
     */
    private function getSlotConfig(string $slotId, string $navigationId, SalesChannelContext $context, ?string $entityName = null): array
    {
        $mailConfigs['receivers'] = [];
        $mailConfigs['message'] = '';
        $criteria = new Criteria([$navigationId]);
        switch ($entityName) {
            case ProductDefinition::ENTITY_NAME:
                $entity = $this->productRepository->search($criteria, $context->getContext())->first();
                break;
            case LandingPageDefinition::ENTITY_NAME:
                $entity = $this->landingPageRepository->search($criteria, $context->getContext())->first();
                break;
            default:
                $entity = $this->categoryRepository->search($criteria, $context->getContext())->first();
        }
        if (!$entity) {
            return $mailConfigs;
        }
        if (empty($entity->getSlotConfig()[$slotId])) {
            return $mailConfigs;
        }
        $mailConfigs['receivers'] = $entity->getSlotConfig()[$slotId]['mailReceiver']['value'];
        $mailConfigs['message'] = '';
        return $mailConfigs;
    }
    
    /**
     * Sends an email using the specified email template and provided data.
     *
     * This method compiles the necessary email data including recipients, template content,
     * attachments, and form data, then sends the email through the mail service.
     *
     * @param array|string $recipients Email addresses of the recipients.
     * @param MailTemplateEntity $mailTemplate The email template to use for the email content.
     * @param array $attachments Files to be attached to the email.
     * @param SalesChannelContext $salesChannelContext The sales channel context for the email.
     * @param DataBag $formData Form data submitted by the user to be included in the email.
     * @return void
     */
    private function sendMail($recipients, $mailTemplate, array $attachments, SalesChannelContext $salesChannelContext, $formData)
    {
        $data = new DataBag();
        $data->set('recipients', $recipients);
        $data->set('senderName', $mailTemplate->getTranslation('senderName'));
        $data->set('templateId', $mailTemplate->getId());
        $data->set('contentHtml', $mailTemplate->getTranslation('contentHtml'));
        $data->set('contentPlain', $mailTemplate->getTranslation('contentPlain'));
        $data->set('subject', $mailTemplate->getTranslation('subject'));
        //set sales channel context
        $data->set('salesChannelId', $salesChannelContext->getSalesChannel()->getId());
        if (!empty($attachments)) {
            $data->set('binAttachments', $attachments);
        }
        $templateData['applicationFormData'] = ['gender' => $formData->get('gender', ''), 'firstName' => $formData->get('firstName', ''), 'lastName' => $formData->get('lastName', ''), 'email' => $formData->get('email', ''), 'street' => $formData->get('street', ''), 'plz_ort' => $formData->get('plz_ort', ''), 'phone' => $formData->get('phone', ''), 'comment' => $formData->get('comment', '')];
        $this->mailService->send($data->all(), $salesChannelContext->getContext(), $templateData);
        dd($data->all(), $salesChannelContext->getContext(), $templateData);
    }
    
    /**
     * Retrieves the mail template entity based on the specified context.
     * This method searches for a mail template type with a specific technical name and returns the first associated mail template entity found.
     *
     * @param Context $context The context in which the search is performed.
     *
     * @return MailTemplateEntity|null The first mail template entity found, or null if none is found.
     */
    private function getMailTemplate(Context $context): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mail_template_type.technicalName', 'application_mail_template_type'));
        $criteria->addAssociation('mailTemplates');
        $criteria->setLimit(1);
        /** @var MailTemplateTypeEntity */
        $mailTemplateType = $this->mailTemplateTypeRepository->search($criteria, $context)->first();
        return $mailTemplateType->getMailTemplates()->first();
    }
    
    /**
     * Retrieves the mail configurations for a given slot within a sales channel context.
     *
     * If a slot ID is provided, it fetches the mail configurations associated with that slot.
     * If a navigation ID is also provided, it attempts to retrieve the slot configuration using both identifiers.
     *
     * @param SalesChannelContext $context The sales channel context.
     * @param string|null $slotId The ID of the slot to retrieve mail configurations for, or null.
     * @param string|null $navigationId The ID of the navigation element, if applicable, or null.
     * @param string|null $entityName The name of the entity, if applicable, or null.
     * @return array The mail configurations including receivers and message.
     */
    private function getMailConfigs(SalesChannelContext $context, ?string $slotId = null, ?string $navigationId = null, ?string $entityName = null): array
    {
        $mailConfigs['receivers'] = [];
        $mailConfigs['message'] = '';
        if (!$slotId) {
            return $mailConfigs;
        }
        if ($navigationId) {
            $mailConfigs = $this->getSlotConfig($slotId, $navigationId, $context, $entityName);
            if (!empty($mailConfigs['receivers'])) {
                return $mailConfigs;
            }
        }
        $criteria = new Criteria([$slotId]);
        $slot = $this->cmsSlotRepository->search($criteria, $context->getContext());
        $mailConfigs['receivers'] = $slot->getEntities()->first()->getTranslated()['config']['mailReceiver']['value'];
        return $mailConfigs;
    }
    
    /**
     * Prepares and validates file attachments from the $_FILES global array.
     *
     * This method checks for the presence of specific attachment types, validates their
     * file extensions and sizes, and prepares them for further processing. If any file
     * does not meet the criteria, appropriate exceptions are thrown.
     *
     * @throws FileTypeNotAllowedException If the file extension is not allowed.
     * @throws ValidatorException If the file size exceeds the allowed limit.
     *
     * @return array An array of attachments, each containing 'content', 'fileName', and 'mimeType'.
     */
    protected function prepareAttachments()
    {
        $allowedext = array('doc', 'docx', 'pdf');
        $attachments = [];
        $attachmentTypes = ['cover_letter', 'cv', 'certificates', 'reference'];
        foreach ($attachmentTypes as $attachmentType) {
            if (is_uploaded_file($_FILES[$attachmentType]['tmp_name'])) {
                $ext = explode(".", $_FILES[$attachmentType]['name']);
                if (!in_array(end($ext), $allowedext)) {
                    throw new FileTypeNotAllowedException(end($ext), "documents");
                } else if ($_FILES[$attachmentType]['size'] > 5242880) {
                    //5 MB
                    throw new ValidatorException("File Size Exceeded");
                } else {
                    $attachments[] = ['content' => file_get_contents($_FILES[$attachmentType]['tmp_name']), 'fileName' => $_FILES[$attachmentType]['name'], 'mimeType' => mime_content_type($_FILES[$attachmentType]['tmp_name'])];
                }
            }
        }
        return $attachments;
    }
}