<?php
/**
 * File defining ZendHttpAuthenticator
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Utilities;
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/share/php/libzend-framework-php');
require_once 'Zend/Loader/Autoloader.php';
\Zend_Loader_Autoloader::getInstance();
/**
 * Wrapper for the the Zend_Auth_Adapter_Http
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class ZendHttpAuthenticator
    implements \Backend\Base\Interfaces\AuthenticatorInterface
{
    /**
     * The constructor for the object
     *
     * @param array $options Options for the Zend_Auth_Adapter_Http object
     */
    public function __construct(array $options = array())
    {
        $this->adapter = new \Zend_Auth_Adapter_Http($options);
        $file = new \Zend_Auth_Adapter_Http_Resolver_File(PROJECT_FOLDER . '/configs/password.txt');
        $this->adapter->setBasicResolver($file);
        //TODO This works, we just translate it into a Backend Response.
        //Not sure if this is *the* best way to do it, though
        $this->adapter->setRequest(new \Zend_Controller_Request_Http());
        $this->adapter->setResponse(new \Zend_Controller_Response_Http());
    }

    /**
     * Authenticate the request
     *
     * @return mixed Either false, true, or a Response Object
     */
    public function authenticate()
    {
        $result = $this->adapter->authenticate();
        if ($result->getCode() === 1) {
            return true;
        }
        $zendResponse = $this->adapter->getResponse();
        $response = new \Backend\Core\Response($zendResponse->getBody(), $zendResponse->getHttpResponseCode());
        foreach ($zendResponse->getHeaders() as $header) {
            $response->addHeader($header['name'], $header['value']);
        }
        return $response;
    }
}
