<?php

declare(strict_types=1);

namespace TocafixTheme\Storefront\Route;

use TocafixTheme\Storefront\Route\Event\ApplicationFormConfirmationEvent;
use Shopware\Core\Content\Mail\Service\AbstractMailService;
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
use Shopware\Core\PlatformRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Psr\Log\LoggerInterface;

/**
 * @RouteScope(scopes={"store-api"})
 */
#[Route(defaults: ['_routeScope' => ['store-api']])]

class JobApplicationFormRoute extends ApplicationFormRoute
{
    protected DataValidationFactoryInterface $applicationFormValidationFactory;
    protected DataValidator $validator;
    protected EventDispatcherInterface $eventDispatcher;
    protected SystemConfigService $systemConfigService;
    protected EntityRepository $cmsSlotRepository;
    protected EntityRepository $categoryRepository;
    protected EntityRepository $landingPageRepository;
    protected EntityRepository $productRepository;
    protected RequestStack $requestStack;
    protected RateLimiter $rateLimiter;
    protected EntityRepository $mailTemplateTypeRepository;
    protected EntityRepository $mailTemplateRepository;
    protected AbstractMailService $mailService;
    protected LoggerInterface $logger;

    public function __construct(
        DataValidationFactoryInterface $applicationFormValidationFactory,
        DataValidator $validator,
        EventDispatcherInterface $eventDispatcher,
        SystemConfigService $systemConfigService,
        EntityRepository $cmsSlotRepository,
        EntityRepository $categoryRepository,
        EntityRepository $landingPageRepository,
        EntityRepository $productRepository,
        RequestStack $requestStack,
        RateLimiter $rateLimiter,
        EntityRepository $mailTemplateTypeRepository,
        EntityRepository $mailTemplateRepository,
        AbstractMailService $mailService,
        LoggerInterface $logger
    ) {
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
        $this->mailTemplateRepository = $mailTemplateRepository;
        $this->mailService = $mailService;
        $this->logger = $logger;
    }

    public function load(RequestDataBag $data, SalesChannelContext $context): ApplicationFormRouteResponse
    {
        try {
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

            $mailTemplate = $this->getMailTemplate($context->getContext(), $context->getSalesChannel()->getId());

            if (!$mailTemplate) {
                throw new \RuntimeException('Mail template not found for job application form');
            }

           
            try {
                $this->sendMail($recipientStructs, $mailTemplate, $attachments, $context, $data);
            } catch (\Throwable $e) {
                $this->logger->error('Job Application Form: Failed to send email', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                    'recipients' => array_keys($recipientStructs)
                ]);
                throw $e;
            }

            // $customerEmail = [$data->get('email') => $data->get('firstName') . ' ' . $data->get('lastName')];
            // $event = new ApplicationFormConfirmationEvent(
            //     $context->getContext(),
            //     $context->getSalesChannel()->getId(),
            //     new MailRecipientStruct($customerEmail),
            //     $data
            // );
            // $this->eventDispatcher->dispatch($event, ApplicationFormConfirmationEvent::EVENT_NAME);

            $result = new ApplicationFormRouteResponseStruct();
            $result->assign(['individualSuccessMessage' => $mailConfigs['message'] ?? '']);

            return new ApplicationFormRouteResponse($result);
        } catch (\Throwable $e) {
            throw $e;
        }
    }

    protected function sendMail($recipients, ?MailTemplateEntity $mailTemplate, array $attachments, SalesChannelContext $salesChannelContext, $formData): void
    {
        if (!$mailTemplate) {
            throw new \RuntimeException('Mail template not found');
        }

        $data = new DataBag();
        $data->set('recipients', $recipients);
        $data->set('senderName', $mailTemplate->getTranslation('senderName') ?? $salesChannelContext->getSalesChannel()->getName());

        $senderEmail = $this->systemConfigService->get('core.basicInformation.email', $salesChannelContext->getSalesChannel()->getId());
       
        $data->set('senderEmail', $senderEmail);
        $data->set('templateId', $mailTemplate->getId());
        $data->set('contentHtml', $mailTemplate->getTranslation('contentHtml'));
        $data->set('contentPlain', $mailTemplate->getTranslation('contentPlain'));
        $data->set('subject', $mailTemplate->getTranslation('subject'));
        $data->set('salesChannelId', $salesChannelContext->getSalesChannel()->getId());

        if (!empty($attachments)) {
            $data->set('binAttachments', $attachments);
        }

        $salesChannel = $salesChannelContext->getSalesChannel();
        $salesChannelName = $salesChannel->getName();
        if (empty($salesChannelName)) {
            $salesChannelName = $salesChannel->getTranslated()['name'] ?? 'Shop';
        }
        $navigationCategoryId = $salesChannel->getNavigationCategoryId();
        if (empty($navigationCategoryId)) {
            $this->logger->warning('Job Application Form: Sales channel has no navigation category ID', [
                'salesChannelId' => $salesChannel->getId()
            ]);
        }
        $templateData = [
            'applicationFormData' => [
                'gender' => $formData->get('gender', ''),
                'firstName' => $formData->get('firstName', ''),
                'lastName' => $formData->get('lastName', ''),
                'email' => $formData->get('email', ''),
                'street' => $formData->get('street', ''),
                'plz_ort' => $formData->get('plz_ort', ''),
                'phone' => $formData->get('phone', ''),
                'comment' => $formData->get('comment', ''),
                'jobTitle' => $formData->get('jobTitle', '')
            ],
            'salesChannelName' => $salesChannelName,
            'salesChannelId' => $salesChannelContext->getSalesChannel()->getId(),
            'navigationCategoryId' => $navigationCategoryId
        ];

        $currentRequest = $this->requestStack->getCurrentRequest();
        $originalNavigationId = null;
        $originalContext = null;

        if ($currentRequest) {
            if ($navigationCategoryId) {
                $originalNavigationId = $currentRequest->get('navigationId');
                $currentRequest->query->set('navigationId', $navigationCategoryId);
            }

            $originalContext = $currentRequest->attributes->get(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
            if (!$originalContext instanceof SalesChannelContext) {
                $currentRequest->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT, $salesChannelContext);
            }
        }

        try {
            $email = $this->mailService->send($data->all(), $salesChannelContext->getContext(), $templateData);
        } finally {
            if ($currentRequest) {
                if ($originalNavigationId !== null) {
                    $currentRequest->query->set('navigationId', $originalNavigationId);
                } elseif ($navigationCategoryId) {
                    $currentRequest->query->remove('navigationId');
                }

                if ($originalContext !== null) {
                    $currentRequest->attributes->set(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT, $originalContext);
                } elseif (!$originalContext instanceof SalesChannelContext) {
                    $currentRequest->attributes->remove(PlatformRequest::ATTRIBUTE_SALES_CHANNEL_CONTEXT_OBJECT);
                }
            }
        }

        if ($email === null) {
            $this->logger->error('Job Application Form: MailService returned null - email sending failed');
            throw new \RuntimeException('Email sending failed - MailService returned null. Check logs for details.');
        }
    }

    private function getMailTemplate(Context $context, string $salesChannelId): ?MailTemplateEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('mailTemplateType.technicalName', 'job_application_mail_template_type'));
        $criteria->addAssociation('mailTemplateType');
        $criteria->setLimit(1);

        return $this->mailTemplateRepository->search($criteria, $context)->first();
    }

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
