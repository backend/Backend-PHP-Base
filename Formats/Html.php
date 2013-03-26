<?php
/**
 * File defining \Base\Formats\Html
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Formats
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Formats;

use Backend\Interfaces\RequestInterface;
use Backend\Interfaces\ResponseInterface;
use Backend\Interfaces\ConfigInterface;
use Backend\Interfaces\RenderInterface;
use Backend\Base\Utilities\Renderable;

/**
 * Output a request as HTML.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Formats
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Html extends \Backend\Core\Utilities\Formatter
{
    /**
     * Relavant configuration options.
     *
     * @var \Backend\Interfaces\ConfigInterfaces
     */
    protected $config;

    /**
     * Rendering Utility used by this formatter.
     *
     * @var \Backend\Interface\RenderInterface
     */
    protected $render;

    /**
     * @var array Handle HTML requests
     */
    public static $handledFormats = array(
        'html', 'htm', 'text/html', 'application/xhtml+xml'
    );

    /**
     * @var array An array of commonly used values
     */
    protected $values = array();

    /**
     * The constructor for the object
     *
     * @param \Backend\Interfaces\RequestInterface $request The request used to
     * determine what formatter to return.
     * @param \Backend\Interfaces\ConfigInterface $config The current Application
     * configuration.
     * @param \Backend\Interfaces\RenderInterface $render A rendering utility.
     */
    public function __construct(RequestInterface $request, ConfigInterface $config, RenderInterface $render)
    {
        parent::__construct($request);

        $this->config = $config;
        $this->values = $this->config->get('values');
        $this->values = empty($this->values) ? array() : $this->values;
        $this->render = $render;

        self::setupConstants();
    }

    /**
     * Set up a number of constants / variables to make creating and parsing
     * templates easier.
     *
     * @return null
     */
    private function setupConstants()
    {
        $context = new \Backend\Core\Utilities\RequestContext($this->request);

        if (defined('SITE_FOLDER') === false) {
            define('SITE_FOLDER', $context->getFolder());
        }
        $this->values['SITE_FOLDER'] = SITE_FOLDER;

        if (defined('SITE_DOMAIN') === false) {
            define('SITE_DOMAIN', $context->getHost());
        }
        $this->values['SITE_DOMAIN'] = SITE_DOMAIN;

        if (defined('SITE_LINK') === false) {
            define('SITE_LINK', $context->getLink());
        }
        $this->values['SITE_LINK'] = SITE_LINK;

        $this->values['context'] = $context;

        $this->values['SITE_STATE'] = defined('BACKEND_SITE_STATE') ? BACKEND_SITE_STATE : 'Unknown';
    }

    /**
     * Return the current configuration.
     *
     * @return Backend\Interfaces\ConfigInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set the configuration to use.
     *
     * @param Backend\Interfaces\ConfigInterface $config The config to set.
     *
     * @return Backend\Base\Formats\Html
     */
    public function setConfig(\Backend\Interfaces\ConfigInterface $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Return the current Renderer.
     *
     * @return Backend\Interfaces\RenderInterface
     */
    public function getRender()
    {
        return $this->render;
    }

    /**
     * Set the Renderer to use.
     *
     * @param Backend\Interfaces\RenderInterface $render The Renderer to set.
     *
     * @return Backend\Base\Formats\Html
     */
    public function setRender(\Backend\Interfaces\RenderInterface $render)
    {
        $this->render = $render;

        return $this;
    }

    /**
     * Transform the result into a Response Object containing the result as HTML
     *
     * @param mixed $result The result to transform into HTML
     *
     * @return \Backend\Core\Response The response transformed into a HTML Response
     * @todo Make the content type header customizable
     */
    public function transform($result)
    {
        // Check for an already created response
        if ($result instanceof ResponseInterface) {
            return parent::transform($result);
        }

        $response = parent::transform($result);
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');

        $body = $response->getBody();
        // Transform the Response Code
        if ($body instanceof \Exception) {
            $code = $body->getCode();
            if ($code > 600 || $code < 100) {
                $code = 500;
            }
            $response->setStatusCode($code);
        }

        // Transform the Body
        if (!is_string($body) || strlen($body) === strlen(strip_tags($body))) {
            $body = $this->transformBody($body);
        }
        $response->setBody($body);

        return $response;
    }

    /**
     * Transform the Body of the response
     *
     * @param mixed $body The body to transform
     *
     * @return string The transformed body
     */
    protected function transformBody($body)
    {
        //Check for an Object
        if (is_object($body)) {
            $body = $this->transformObject($body);
        } else {
            if (is_array($body)) {
                $body = var_export($body, true);
            }
            $this->values['content'] = $body;
            $body = $this->render->file('index', $this->values);
        }

        return $body;
    }

    /**
     * Transform an object into a renderable string
     *
     * @param object $object The object to transform
     *
     * @return string The object transformed into a string
     */
    protected function transformObject($object)
    {
        $template = 'base.twig';
        $values   = $this->values;
        switch (true) {
            case $object instanceof Renderable:
                $template = $object->getTemplate();
                $values   = array_merge($values, $object->getValues());
                break;
            case $object instanceof \Exception:
                $template            = 'exception';
                $values['title']     = get_class($object);
                $values['message']   = $object->getMessage();
                if (property_exists($object, 'xdebug_message')) {
                    $values['xdebug_message'] = $object->xdebug_message;
                }
                $values['exception'] = $object;
                break;
            default:
                break;
        }

        return $this->render->file($template, $values);
    }

    /**
     * Set a value to the values array.
     *
     * @param string $name  The name of the value
     * @param mixed  $value The value
     *
     * @return \Backend\Base\Formats\Html The current object.
     */
    public function setValue($name, $value)
    {
        $this->values[$name] = $value;
        return $this;
    }

    /**
     * Get the specified value, or all of the values if none is specified.
     *
     * @param string $name The name of the value. Omit to get all of the values.
     *
     * @return mixed The value, or an array of all the values.
     */
    public function getValue($name = null)
    {
        if ($name === null) {
            return $this->values;
        }
        return array_key_exists($name, $this->values) ? $this->values[$name] : null;
    }
}
