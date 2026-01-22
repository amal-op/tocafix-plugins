<?php

declare(strict_types=1);

namespace TocafixTheme\Storefront\Controller;

use TocafixTheme\Storefront\Route\JobApplicationFormRoute;
use Shopware\Core\Framework\RateLimiter\Exception\RateLimitExceededException;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Routing\Annotation\Since;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Framework\Validation\DataBag\DataBag;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\Framework\Validation\Exception\ConstraintViolationException;
use Shopware\Storefront\Framework\Media\Exception\FileTypeNotAllowedException;
use Symfony\Component\Validator\Exception\ValidatorException;

/**
 * Class JobApplicationSendController
 *
 * This class is responsible for handling the job application form submission process.
 * It offers methods for initializing the job application form route, processing and validating form data,
 * generating HTML templates for the form, and preparing file attachments.
 * These operations ensure a streamlined and secure application submission experience, with proper validation
 * and error handling for form inputs and file uploads.
 * 
 */
#[Route(defaults: ["_routeScope" => ["storefront"]])]
class JobApplicationSendController extends StorefrontController
{

    /**
     * The route for the job application form.
     *
     * @var JobApplicationFormRoute
     */
    private $applicationFormRoute;

    /**
     * Constructor method for initializing the JobApplicationFormRoute.
     *
     * This method sets the application form route for the job application form.
     *
     * @param JobApplicationFormRoute $applicationFormRoute The route object for the job application form.
     */
    public function __construct(JobApplicationFormRoute $applicationFormRoute)
    {
        $this->applicationFormRoute = $applicationFormRoute;
    }

    /**
     * Handles the submission of the job application form. Processes the form data, validates it, 
     * and returns a JSON response with success or error messages based on the outcome.
     *
     * @Since("6.1.0.0")
     *
     * @param RequestDataBag $data The data bag containing the form submission data.
     * @param SalesChannelContext $context The context of the current sales channel.
     * 
     * @return JsonResponse A JSON response indicating the result of the form submission.
     */
    #[Route("/job/tocafix-form-submit", name: "frontend.tocafix-job-form.send", methods: ["POST"], defaults: ["XmlHttpRequest" => true])]
    public function sendApplicationForm(RequestDataBag $data, SalesChannelContext $context): JsonResponse
    {
        $response = [];
        try {
            $message = $this->applicationFormRoute->load($data->toRequestDataBag(), $context)->getResult()->getIndividualSuccessMessage();
            if (!$message) {
                $message = $this->trans('job.detail.applicationForm.successMessage');
            }
            $response[] = [
                'type' => 'success',
                'alert' => $message,
            ];
        } catch (ConstraintViolationException $formViolations) {
            $violations = [];
            foreach ($formViolations->getViolations() as $violation) {
                $violations[] = $violation->getMessage();
            }
            $response[] = [
                'type' => 'danger',
                'alert' => $violations,
            ];
        } catch (RateLimitExceededException $exception) {
            $response[] = [
                'type' => 'info',
                'alert' => $this->trans('error.rateLimitExceeded', ['%seconds%' => $exception->getWaitTime()]),
            ];
        } catch (FileTypeNotAllowedException $exception) {
            $response[] = [
                'type' => 'danger',
                'alert' => $this->trans('error.VIOLATION::STRICT_CHECK_FAILED_ERROR', ['%field%' => 'document']),
            ];
        } catch (ValidatorException $exception) {
            $response[] = [
                'type' => 'danger',
                'alert' => $this->trans('error.VIOLATION::STRICT_CHECK_FAILED_ERROR', ['%field%' => 'document']),
            ];
        }
        return new JsonResponse($response);
    }

    public function sendMail(array $recipients, string $senderName, string $subject, string $messageHtml, array $attachments, SalesChannelContext $salesChannelContext)
    {
        $data = new DataBag();
        //basic e-mail data
        $data->set('recipients', $recipients);
        //format: ['email address' => 'recipient name']
        $data->set('senderName', $senderName);
        $data->set('subject', $subject);
        $data->set('contentHtml', $messageHtml);
        $data->set('contentPlain', strip_tags($messageHtml));
        //set sales channel context
        $data->set('salesChannelId', $salesChannelContext->getSalesChannel()->getId());
        if (!empty($attachments)) {
            $data->set('binAttachments', $attachments);
        }
        //send the e-mail
        $this->mailService->send($data->all(), $salesChannelContext->getContext(), []);
    }
}
