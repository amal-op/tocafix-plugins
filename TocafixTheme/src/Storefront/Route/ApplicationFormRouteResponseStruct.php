<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Route;

use Shopware\Core\Framework\Struct\Struct;

/**
 * Class ApplicationFormRouteResponseStruct
 *
 * This class is responsible for handling the responses related to application form routes.
 * It offers methods for retrieving crucial information such as API aliases and success messages,
 * ensuring that the application can consistently and accurately communicate the status of form submissions and related actions.
 * 
 * The class provides:
 * - A method to get the API alias for application form results.
 * - A method to fetch individual success messages.
 * 
 * These functionalities guarantee that the application can handle responses with a standardized and clear approach, enhancing the reliability of user interactions.
 */
class ApplicationFormRouteResponseStruct extends Struct
{
    
    /**
     * The message displayed upon the successful completion of an individual operation.
     *
     * @var string
     */
    protected $individualSuccessMessage;
    
    /**
     * Retrieves the API alias for the application form result.
     *
     * @return string The API alias 'application_form_result'.
     */
    public function getApiAlias(): string
    {
        return 'application_form_result';
    }
    
    /**
     * Retrieves the individual success message.
     *
     * This method returns the message that signifies the success of an individual action or process.
     *
     * @return string The individual success message.
     */
    public function getIndividualSuccessMessage(): string
    {
        return $this->individualSuccessMessage;
    }
}