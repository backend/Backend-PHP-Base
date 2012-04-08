<?php
/**
 * File defining Backend\Base\Decorators\ProtectedController
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Interfaces
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Decorators;
use Backend\Core\Application;
use Backend\Core\Decorators\Decorator;
use Backend\Core\Interfaces\DecorableInterface;
/**
 * The Protected Controller is a Decorator that adds access control to the controller
 *
 * @category   Backend
 * @package    Base
 * @subpackage Interfaces
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class ProtectedController extends Decorator
{
    /**
     * @var Backend\Base\Interfaces\AuthenticatorProvider The Authenticator Provider
     */
    protected $authenticator = null;

    /**
     * @var Backend\Base\Interfaces\AccessControlProvider The Access Control Provider
     */
    protected $accessControl = null;

    /**
     * The constructor for the decorator
     *
     * @param \Backend\Core\Interfaces\DecorableInterface    $object        The object to decorate
     * @param \Backend\Core\Interfaces\AuthenticatorProvider $authenticator The Authenticator Provider
     * @param \Backend\Core\Interfaces\AccessControlProvider $accessControl The Access Control Provider
     */
    function __construct(DecorableInterface $object,
        AuthenticatorProviderInterface $authenticator = null,
        AccessControlProviderInterface $accessControl = null
    ) {
        $this->authenticator = $authenticator ?: Application::getTool('AuthenticatorProvider');
        $this->accessControl = $accessControl ?: Application::getTool('AccessControlProvider');
        parent::__construct($object);
    }

    /**
     * The magic __call function to pass on calls to decorated object
     *
     * This is used to call the specified function on the original object
     * after the current authenticator provider is checked for a valid user
     *
     * @param string $method The name of the method to call
     * @param array  $args   The arguments to pass to the method
     *
     * @return mixed The result of the called method
     */
    public function __call($method, $args)
    {
        //TODO Cache this somehow
        if (file_exists(PROJECT_FOLDER . 'configs/protected_controller.yaml')) {
            $config = new \Backend\Core\Utilities\Config(PROJECT_FOLDER . 'configs/protected_controller.yaml');
            $whitelist = $config->get(get_class($this->getOriginalObject()), 'whitelist');
        } else {
            $whitelist = array();
        }
        if (in_array($method, $whitelist)) {
            return parent::__call($method, $args);
        }
        $result = $this->checkAuthenticated();
        if ($result instanceof \Backend\Core\Response) {
            return $result;
        }
        if (!$result) {
            //TODO Extend this exception
            throw new \Exception('Unauthorized Access');
        }
        /*if (!$this->checkAccessControl($user, array($this->getOriginalObject(), $method))) {
            //TODO Extend this exception
            throw new \Exception('Unauthorized Access');
        }*/
        return parent::__call($method, $args);
    }

    /**
     * Check the current Authenticator provider for an authenticated user
     *
     * @todo Should we just return true if there's no Authenticator Provider? or throw an exception?
     * @todo Fix the return type
     * @return mixed false if there's no autenticated user, otherwise the authenticated user
     */
    public function checkAuthenticated()
    {
        //Check if we have an Authenticator
        if (!$this->authenticator) {
            return true;
        }
        //Check if we can authenticate the user
        $result = $this->authenticator->authenticate();
        if (!$this->authenticator->authenticate()) {
            return false;
        }
        return $result;
        $user = $this->authenticator->getAuthenticatedUser();
        return $user;
    }

    /**
     * Check if the supplied user has access to the specified callback
     *
     * @param AuthenticatedUser $user     User to check
     * @param callback          $callback The callback to check against
     *
     * @todo Fix the param type for user
     * @return boolean If the user has access to the callback
     */
    public function checkAccessControl($user, $callback)
    {
        if (!$this->accessControl) {
            return true;
        }
        return $this->accessControl->checkCallback($user, $callback);
    }
}
