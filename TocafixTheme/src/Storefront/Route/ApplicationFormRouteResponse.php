<?php

declare (strict_types=1);
namespace TocafixTheme\Storefront\Route;

use Shopware\Core\System\SalesChannel\StoreApiResponse;

/**
 * Class ApplicationFormRouteResponse
 *
 * This class is responsible for handling responses related to application form routes.
 * It offers methods for initializing the response object and retrieving the result as an ApplicationFormRouteResponseStruct object,
 * ensuring that the response is structured and accessible in a consistent manner.
 * 

 */
class ApplicationFormRouteResponse extends StoreApiResponse
{
    
    /**
     * The response structure object for the application form route.
     *
     * @var ApplicationFormRouteResponseStruct
     */
    protected $object;
    
    /**
     * Constructor method for initializing the ApplicationFormRouteResponseStruct object.
     *
     * This method calls the parent constructor to initialize the object.
     *
     * @param ApplicationFormRouteResponseStruct $object The object to initialize.
     */
    public function __construct(ApplicationFormRouteResponseStruct $object)
    {
        parent::__construct($object);
    }
    
    /**
     * Retrieves the result as an ApplicationFormRouteResponseStruct object.
     *
     * @return ApplicationFormRouteResponseStruct The result object.
     */
    public function getResult(): ApplicationFormRouteResponseStruct
    {
        return $this->object;
    }
}