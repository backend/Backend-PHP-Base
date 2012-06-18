<?php
/**
 * File defining \Backend\Base\Application
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   Base
 * @author    J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright 2011 - 2012 Jade IT (cc)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://backend-php.net
 */
namespace Backend\Base;
use Backend\Core\Application as CoreApplication;
use Backend\Interfaces\ApplicationInterface;
use Backend\Interfaces\RouterInterface;
use Backend\Interfaces\FormatterInterface;
use Backend\Interfaces\RequestInterface;
use Backend\Interfaces\CallbackInterface;
use Backend\Interfaces\ConfigInterface;
use Backend\Core\Utilities\Router;
use Backend\Core\Utilities\Formatter;
use Backend\Modules\Callback;
use Backend\Modules\Config;
/**
 * The main application class.
 *
 * @category Backend
 * @package  Base
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class Application extends CoreApplication implements ApplicationInterface
{
    protected $config;
    protected $render;

    /**
     * The constructor for the object.
     *
     * @param \Backend\Interfaces\RouterInterface    $router    The router to use.
     * @param \Backend\Interfaces\FormatterInterface $formatter The formatter to use.
     * @param \Backend\Interfaces\ConfigInterface    $config    The configuration
     * to use for this application.
     */
    public function __construct(
        RouterInterface $router = null,
        FormatterInterface $formatter = null,
        ConfigInterface $config = null
    ) {
        parent::__construct($router, $formatter);
        $this->config = $config ?: new Config($this->getConfigFile());
    }

    /**
     * Get the configuration file for the application.
     *
     * @return string
     */
    protected function getConfigFile()
    {
        if (file_exists(
            PROJECT_FOLDER . 'configs/' . BACKEND_SITE_STATE . '.yaml'
        )) {
            return PROJECT_FOLDER . 'configs/' . BACKEND_SITE_STATE . '.yaml';
        } else if (file_exists(PROJECT_FOLDER . 'configs/default.yaml')) {
            return PROJECT_FOLDER . 'configs/default.yaml';
        } else {
            $string = 'Could not find Application Configuration file. . Add one to '
                . PROJECT_FOLDER . 'configs';
            throw new ConfigException($string);
        }
    }

    /**
     * Get the appropriate formatter object.
     *
     * @param \Backend\Interfaces\RequestInterface $request The request to
     * determine what formatter to return.
     * @param \Backend\Interfaces\ConfigInterface  $config  The current Application
     * configuration.
     *
     * @return \Backend\Interfaces\FormatterInteface
     */
    public function getFormatter(
        RequestInterface $request = null, ConfigInterface $config = null
    ) {
        $config = $config ?: $this->config;
        return parent::getFormatter($request, $config);
    }
}
