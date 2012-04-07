<?php
namespace Backend\Base\Utilities;
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/share/php/libzend-framework-php');
require_once 'Zend/Loader/Autoloader.php';
$autoloader = \Zend_Loader_Autoloader::getInstance();
class ZendHttpAuthenticator
    implements \Backend\Base\Interfaces\AuthenticatorInterface
{
    public function __construct(array $options = array())
    {
        $this->adapter = new \Zend_Auth_Adapter_Http($options);
        $file = new \Zend_Auth_Adapter_Http_Resolver_File(PROJECT_FOLDER . '/configs/password.txt');
        $this->adapter->setBasicResolver($file);
        $this->adapter->setRequest(new \Zend_Controller_Request_Http());
        $this->adapter->setResponse(new \Zend_Controller_Response_Http());
    }

    public function authenticate()
    {
        $result = $this->adapter->authenticate();
        if ($result->getCode() === 1) {
            return true;
        }
        $zendResponse = $this->adapter->getResponse();
        $response = new \Backend\Core\Response($zendResponse->getBody(), $zendResponse->getHttpResponseCode());
        foreach($zendResponse->getHeaders() as $header) {
            $response->addHeader($header['name'], $header['value']);
        }
        return $response;
    }
}
