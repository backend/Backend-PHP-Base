<?php
/**
 * File defining Base\Utilities\Render
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
use Backend\Core\Utilities\ApplicationEvent;
/**
 * The basic Render class.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Render implements \Backend\Base\Interfaces\RenderUtilityInterface
{
    /**
     * @var array Location for template files. List them in order of preference
     */
    protected $templateLocations = array();

    /**
     * @var array This contains the variables bound to the renderer
     */
    protected $variables = array();

    /**
     * The constructor for the object
     *
     * The template locations for the Renderer is set in this method
     */
    public function __construct()
    {
        $this->templateLocations = array();
        //Check Application Folder
        $this->templateLocations = array_merge($this->templateLocations, glob(SOURCE_FOLDER. '*/*/templates/', \GLOB_ONLYDIR));

        //Add Project wide templates
        $this->templateLocations[] = PROJECT_FOLDER . 'templates/';

        //Check Vendor Folder
        $this->templateLocations = array_merge($this->templateLocations, glob(VENDOR_FOLDER . '*/*/templates/', \GLOB_ONLYDIR));

        //Check if they exist
        $this->templateLocations = array_filter($this->templateLocations, 'file_exists');
    }

    /**
     * Bind a variable to the renderer
     *
     * @param string  $name      The name of the variable
     * @param mixed   $value     The value of the variable
     * @param boolean $overwrite Set to false to honor previously set values
     *
     * @return The value of the bound value
     */
    public function bind($name, $value, $overwrite = true)
    {
        if ($overwrite || !array_key_exists($name, $this->variables)) {
            $this->variables[$name] = $value;
        }
        return $this->variables[$name];
    }

    /**
     * Get the value of a variable
     *
     * @param string $name The name of the variable
     *
     * @return mixed The value of the variable
     */
    public function get($name)
    {
        return array_key_exists($name, $this->variables) ? $this->variables[$name] : null;
    }

    /**
     * Get all of the bound variables
     *
     * @return array An array of all the variables bound to the renderer
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Render the specified template, using the given values
     *
     * @param string $template The template to render
     * @param array  $rValues  The values to use to render the template
     *
     * @return string The rendered template
     */
    public function file($template, array $rValues = array())
    {
        $rFile = $this->templateFile($template);
        if (!$rFile) {
            //TODO Throw an exception, make a fuss?
            new ApplicationEvent('Missing Template: ' . $template, ApplicationEvent::SEVERITY_DEBUG);
            return false;
        }

        //TODO Add Caching
        ob_start();
        extract($rValues);
        include $rFile;
        $result = ob_get_clean();

        //Substitute Variables into the templates
        $result = $this->parseVariables($result, $rValues);

        return $result;
    }

    /**
     * Get the file name for the specified template
     *
     * @param string $template The name of the template
     *
     * @return string The absolute path to the template file to render
     */
    protected function templateFile($template)
    {
        $template = $this->templateFileName($template);
        $locations = array();
        if (!empty($this->templateLocations) && is_array($this->templateLocations)) {
            $locations = array_merge($locations, $this->templateLocations);
        }
        foreach (array_unique($locations) as $location) {
            if (file_exists($location . $template)) {
                return $location . $template;
            }
        }
        return false;
    }

    /**
     * Convert the template name to a filename
     *
     * @param string $template The name of the template
     *
     * @return string The filename for the template
     */
    protected function templateFileName($template)
    {
        if (substr($template, -8) != '.tpl.php') {
            $template .= '.tpl.php';
        }
        return $template;
    }

    /**
     * Check the string for variables (#VarName#) and replace them with the appropriate values
     *
     * The values currently bound to the view will be used.
     *
     * @param string $string The string to check for variable names
     * @param array  $values Extra variables to consider
     *
     * @return string The string with the variables replaced
     */
    function parseVariables($string, array $values = array())
    {
        foreach ($values as $name => $value) {
            if (is_string($name) && is_string($value)) {
                $search[] = '#' . $name . '#';
                $replace[] = $value;
            }
        }
        $string = str_replace($search, $replace, $string);
        return $string;
    }
}
