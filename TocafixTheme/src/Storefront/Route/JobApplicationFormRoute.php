<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Route;

use TocafixTheme\Storefront\Route\Event\ApplicationFormConfirmationEvent;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
use Shopware\Core\Content\MailTemplate\Aggregate\MailTemplateType\MailTemplateTypeEntity;
use Shopware\Core\Content\MailTemplate\MailTemplateEntity;
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
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Class JobApplicationFormRoute
 *
 * This class is responsible for handling job application form submissions within the store API scope.
 * It offers methods for sending emails with specified templates and attachments, retrieving mail templates based on context,
 * and fetching mail configuration settings. This ensures robust and configurable job application email handling.
 * 
 * Key operations include:
 * - Sending an email with specified mail template and attachments.
 * - Retrieving the appropriate mail template entity for a given context.
 * - Fetching mail configuration settings based on the sales channel context.
 * 
 * The class guarantees efficient email handling and configuration retrieval, ensuring seamless integration within the store API.
 * 
 * @RouteScope(scopes={"store-api"})
 */
class JobApplicationFormRoute extends ApplicationFormRoute
{
    
    /**
     * Factory for creating data validation instances for application forms.
     *
     * @var DataValidationFactoryInterface
     */
    protected DataValidationFactoryInterface $applicationFormValidationFactory;
    
    /**
     * An instance of DataValidator used to validate data.
     *
     * @var DataValidator
     */
    protected DataValidator $validator;
    
    /**
     * The event dispatcher used to dispatch and manage events within the application.
     *
     * @var EventDispatcherInterface
     */
    protected EventDispatcherInterface $eventDispatcher;
    
    /**
     * Service for handling system configuration settings.
     *
     * @var SystemConfigService
     */
    protected SystemConfigService $systemConfigService;
    
    /**
     * Repository interface for CMS slot entities, providing methods to interact with the data source.
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
     * Repository for managing landing page entities.
     *
     * @var EntityRepository
     */
    protected EntityRepository $landingPageRepository;
    
    /**
     * The repository interface for managing product entities.
     *
     * @var EntityRepository
     */
    protected EntityRepository $productRepository;
    
    /**
     * The stack of requests for the current session.
     *
     * @var RequestStack
     */
    protected RequestStack $requestStack;
    
    /**
     * The rate limiter instance used to control the rate of requests or actions.
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
     * Sends an email using the specified mail template and attachments, and includes additional form data.
     *
     * @param mixed $recipients The recipients of the email.
     * @param MailTemplateEntity $mailTemplate The mail template to be used for the email.
     * @param array $attachments An array of attachments to be included in the email.
     * @param SalesChannelContext $salesChannelContext The sales channel context.
     * @param DataBag $formData The form data to be included in the email.
     *
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
        $templateData['applicationFormData'] = ['gender' => $formData->get('gender', ''), 'firstName' => $formData->get('firstName', ''), 'lastName' => $formData->get('lastName', ''), 'email' => $formData->get('email', ''), 'street' => $formData->get('street', ''), 'plz_ort' => $formData->get('plz_ort', ''), 'phone' => $formData->get('phone', ''), 'comment' => $formData->get('comment', ''), 'jobTitle' => $formData->get('jobTitle', '')];
        $this->mailService->send($data->all(), $salesChannelContext->getContext(), $templateData);
    }
    
    /**
     * Retrieves the mail template entity based on the specified context.
     *
     * This method constructs a criteria to filter mail templates by the technical name
     * 'job_application_mail_template_type', associates the mail templates, and limits the
     * result to one item. It returns the first mail template entity found for the given context.
     *
     * @param Context $context The context in which the mail template is being retrieved.
     * 
     * @return MailTemplateEntity|null The first mail template entity found, or null if none is found.
     */
    private function getMailTemplate(Context $context): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mail_template_type.technicalName', 'job_application_mail_template_type'));
        $criteria->addAssociation('mailTemplates');
        $criteria->setLimit(1);
        /** @var MailTemplateTypeEntity */
        $mailTemplateType = $this->mailTemplateTypeRepository->search($criteria, $context)->first();
        return $mailTemplateType->getMailTemplates()->first();
    }
    
    /**
     * Retrieves the mail configuration settings based on the provided sales channel context.
     * Optionally, it can filter configurations based on slot ID, navigation ID, or entity name.
     *
     * @param SalesChannelContext $context The sales channel context containing necessary configuration and metadata.
     * @param string|null $slotId Optional. The slot ID to filter the configurations.
     * @param string|null $navigationId Optional. The navigation ID to filter the configurations.
     * @param string|null $entityName Optional. The entity name to filter the configurations.
     * 
     * @return array An array containing the mail configuration settings, including receivers and message content.
     */
    private function getMailConfigs(SalesChannelContext $context, ?string $slotId = null, ?string $navigationId = null, ?string $entityName = null): array
    {
        $mailConfigs['receivers'] = [];
        if ($this->systemConfigService->get('TocafixTheme.config.jobApplicationEmail', $context->getSalesChannel()->getId())) {
            $mailConfigs['receivers'][] = $this->systemConfigService->get('TocafixTheme.config.jobApplicationEmail', $context->getSalesChannel()->getId());
        }
        $mailConfigs['message'] = '';
        return $mailConfigs;
    }
}