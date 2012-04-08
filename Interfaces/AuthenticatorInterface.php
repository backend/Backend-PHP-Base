<?php
/**
 * File defining Base\Interfaces\AuthenticatorInterface
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
namespace Backend\Base\Interfaces;
/**
 * Utility to authenticate users
 *
 * The persistance of the authentication should be implemented by the component
 *
 * @category   Backend
 * @package    Base
 * @subpackage Interfaces
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
interface AuthenticatorInterface
{
    /**
     * Authenticate the request
     *
     * @return mixed Either false, true, or a Response Object
     */
    public function authenticate();
}
