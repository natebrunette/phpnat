<?php
/**
 * File XboxApiException.php
 */

namespace Nerdery\Xbox\Bundle\ApiBundle\Exception;

/**
 * Class XboxApiException
 *
 * Throw this exception for errors occurring during communication with the api
 *
 * @author Nate Brunette <n@tebru.net>
 * @package Nerdery\Xbox\Bundle\ApiBundle\Exception
 */
class XboxApiException extends \RuntimeException
{
    /**
     * Error is a Soap error
     */
    const ERROR_CODE_SOAP = 1;

    /**
     * Error is an api error
     */
    const ERROR_CODE_API = 2;

    /**#@+
     * Api error messages
     */
    const ERROR_API_KEY_INVALID = 'The api key is invalid';
    const ERROR_ID_INVALID = 'The id is invalid';
    /**#@-*/
}