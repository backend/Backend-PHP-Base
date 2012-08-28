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
use \Backend\Base\Utilities\Renderable;
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
        $values['session'] = array_key_exists('session', $values) ?
            $values['session'] : $this->session;
        $values['user'] = array_key_exists('user', $values) ?
            $values['user'] : $this->user_session->readAction();
        return new Renderable($this->renderer, $template, $values);
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
        throw new \ErrorException('Undefined property: ' . __CLASS__ . '::$' . $property);
    }
}
