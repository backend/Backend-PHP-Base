<?php
/**
 * File defining Base\Utilities\Renderable
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   Base/Utilities
 * @author    J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright 2011 - 2012 Jade IT (cc)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://backend-php.net
 */
namespace Backend\Base\Utilities;
/**
 * An object that can be Rendered by the Render Utility
 *
 * Return a Renderable object from a Controller if you want to specify what template to render
 *
 * @category Backend
 * @package  Base/Utilities
 * @author   J Jurgens du Toit <jrgns@jrgns.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class Renderable
{
    /**
     * @var string The template to render
     */
    protected $template = 'index';

    /**
     * @var array The values to use when rendering the template
     */
    protected $values = array();

    /**
     * The constructor for the object
     *
     * @param string $template The name of the template for the object
     * @param array  $values   The values to use when rendering the template
     */
    function __construct($template, array $values = array())
    {
        $this->template = $template;
        $this->values = $values;
    }

    /**
     * Get the filename of the current template
     *
     * @return string The filename of the template
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set the template
     *
     * @param string $template The filename of the template
     *
     * @return null
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * Get the current values
     *
     * @return array The current values
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set a single value to be used when rendering the template
     *
     * @param string $name  The name of the value being set
     * @param mixed  $value The value of the value being set
     *
     * @return null
     */
    public function setValue($name, $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * Set the values to be used when rendering the template
     *
     * @param array $values The new values
     *
     * @return null
     */
    public function setValues(array $values)
    {
        $this->values = $values;
    }

    /**
     * The magic function that converts the object to a string
     *
     * @return string The rendered template
     */
    public function __toString()
    {
        return \Backend\Core\Application::getTool('Render')
            ->file($this->template, $this->values);
    }
}
