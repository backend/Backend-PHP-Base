<?php
/**
 * File defining the Base Controller
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   Core
 * @author    J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright 2011 - 2012 Jade IT (cc)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://backend-php.net
 */
namespace Backend\Base;
use Backend\Base\Utilities\Renderable;
/**
 * A Basic Controller that contains Application Logic.
 *
 * @category Backend
 * @package  Core
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class Controller extends \Backend\Core\Controller
{
    /**
     * Create a Renderable object.
     *
     * @param string $template The template to render.
     * @param array  $values   The values to pass to the template.
     *
     * @return \Backend\Base\Utilities\Renderable The Renderable object
     */
    public function render($template, array $values = array())
    {
        // Add the session to the values if it doesn't exist yet
        if ($this->container->has('session') && array_key_exists('session', $values) === false) {
            $values['session'] = $this->container->get('session');
        }
        // Add the user session to the values if it doesn't exist yet
        if ($this->container->has('user_session') && array_key_exists('user', $values) === false) {
            $values['user']  = $this->container->get('user_session')->readAction();
        }
        // Add the flash to the values if it doesn't exist yet
        if ($this->container->has('flash') && array_key_exists('flash', $values) === false) {
            $values['flash'] = $this->container->get('flash');
        }
        return new Renderable($this->container->get('renderer'), $template, $values);
    }

    /**
     * Safely add a flash variable.
     *
     * If the flash service was defined, log the call and ignore.
     *
     * @param string $name  The name of the flash value.
     * @param mixed  $value The flash value.
     *
     * @return \Backend\Base\Controller
     */
    public function flash($name, $value)
    {
        if ($this->container->has('flash')) {
            $this->flash->set($name, $value);
        } else if ($this->container->has('logger')) {
            $this->logger->debug('Trying to set flash variable without flash service');
        }
        return $this;
    }

    /**
     * Magic method to get properties. Will return the named service if it exists.
     *
     * @param string $property The name of the property or service to get.
     *
     * @return object
     * @throws \ErrorException If the service doesn't exist.
     */
    public function __get($property)
    {
        if ($this->container->has($property)) {
            return $this->container->get($property);
        }
        throw new \ErrorException('Undefined property: ' . get_called_class() . '::$' . $property);
    }
}
