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
     * The Renderer to use.
     *
     * @var \Backend\Interfaces\RenderInterface
     */
    protected $renderer = null;

    /**
     * The template to render.
     * 
     * @var string
     */
    protected $template = 'index';

    /**
     * The values to use when rendering the template.
     * 
     * @var array
     */
    protected $values = array();

    /**
     * The constructor for the object
     *
     * @param \Backend\Interfaces\RenderInterface $renderer The Rendering Utility
     * to use.
     * @param string                              $template The name of the
     * template for the object
     * @param array                               $values   The values to use
     * when rendering the template
     */
    function __construct(\Backend\Interfaces\RenderInterface $renderer,
        $template, array $values = array())
    {
        $this->renderer = $renderer;
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
     * @return The current object
     */
    public function setTemplate($template)
    {
        $this->template = $template;
        return $this;
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
     * @return The current object
     */
    public function setValue($name, $value)
    {
        $this->values[$name] = $value;
        return $this;
    }

    /**
     * Set the values to be used when rendering the template
     *
     * @param array $values The new values
     *
     * @return The current object
     */
    public function setValues(array $values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Add multiple values to be used when rendering the template
     *
     * Values already in the collection will be overwritten
     *
     * @param array $values The values to append
     *
     * @return The current object
     */
    public function addValues(array $values)
    {
        $this->values = array_merge($this->values, $values);
        return $this;
    }

    /**
     * The magic function that converts the object to a string
     *
     * @return string The rendered template
     */
    public function __toString()
    {
        try {
            return $this->renderer->file($this->template, $this->values);
        } catch (\Exception $e) {
            return 'There was an error parsing ' . $this->template .': ' . $e->getMessage();
        }
    }
}
