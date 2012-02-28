<?php
/**
 * File defining Base\Utilities\TwigRender
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
/**
 * Render Twig templates.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class TwigRender
    extends \Backend\Base\Utilities\Render
    implements \Backend\Base\Interfaces\RenderUtilityInterface
{
    /**
     * @var Twig The twig used to render templates
     */
    protected $twig = null;

    /**
     * The constructor for the object
     *
     * The template locations for the Renderer is set in this method
     */
    public function __construct()
    {
        parent::__construct();
        array_unshift($this->templateLocations, SOURCE_FOLDER);
        if (!class_exists('\Twig_Autoloader')) {
            include_once 'Twig/Autoloader.php';
        }
        \Twig_Autoloader::register();
        $loader     = new \Twig_Loader_Filesystem($this->templateLocations);
        $this->twig = new \Twig_Environment($loader);
    }

    /**
     * Render the specified template, using the given values
     *
     * @param string $template The template to render
     * @param array  $values   The values to use to render the template
     *
     * @return string The rendered template
     */
    public function file($template, array $values = array())
    {
        //Use templateFileName instead of templateFile.
        //Twig handles it's own locations
        $file = $this->templateFileName($template);

        $values = array_merge($this->getVariables(), $values);

        return $this->twig->render($file, $values);
    }

    /**
     * Get the file name for the specified template
     *
     * @param string $template The name of the template
     *
     * @return string The template file to render
     */
    protected function templateFileName($template)
    {
        if (substr($template, -5) != '.twig') {
            $template .= '.twig';
        }
        $template = str_replace('\\', DIRECTORY_SEPARATOR, $template);
        return $template;
    }
}
