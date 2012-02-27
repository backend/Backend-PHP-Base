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
    implements \Backend\Base\Interfaces\RenderUtility
{
    /**
     * @var Twig The twig used to render templates
     */
    protected $twig = null;

    /**
     * The constructor for the object
     */
    public function __construct()
    {
        parent::__construct();
        array_unshift($this->templateLocations, SOURCE_FOLDER);
        require_once('Twig/Autoloader.php');
        \Twig_Autoloader::register();
        $loader     = new \Twig_Loader_Filesystem($this->templateLocations);
        $this->twig = new \Twig_Environment($loader);
    }

    public function file($template, array $values = array())
    {
        //Use templateFileName instead of templateFile. Twig handles it's own locations
        $file = $this->templateFileName($template);

        $values = array_merge($this->getVariables(), $values);

        return $this->twig->render($file, $values);
    }

    protected function templateFileName($template)
    {
        if (substr($template, -5) != '.twig') {
            $template .= '.twig';
        }
        $template = str_replace('\\', DIRECTORY_SEPARATOR, $template);
        return $template;
    }

}
