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
use \Backend\Interfaces\RequestInterface;
use \Backend\Interfaces\ConfigInterface;
use \Backend\Interfaces\RenderInterface;
use \Backend\Base\Utilities\Renderable;
use \Backend\Core\Decorators\PrettyExceptionDecorator;
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
     * @param \Backend\Interfaces\ConfigInterface  $config  The current Application
     * configuration.
     * @param \Backend\Interfaces\RenderInterface  $render  A rendering utility.
     */
    function __construct(
        RequestInterface $request, ConfigInterface $config, RenderInterface $render
    ) {
        parent::__construct($request);

        $this->config = $config;
        $this->values = $this->config->get('values');
        $this->values = empty($this->values) ? array() : $this->values;
        $this->render = $render;

        self::_setupConstants();
    }

    /**
     * Set up a number of constants / variables to make creating and parsing
     * templates easier.
     *
     * @return null
     */
    private function _setupConstants()
    {
        $urlParts = parse_url($this->request->getUrl());

        if (defined('SITE_FOLDER') === false) {
            define('SITE_FOLDER', dirname($urlParts['path']));
        }
        $this->values['SITE_FOLDER'] = SITE_FOLDER;

        if (defined('SITE_DOMAIN') === false) {
            define('SITE_DOMAIN', $urlParts['host']);
        }
        $this->values['SITE_DOMAIN'] = SITE_DOMAIN;

        if (defined('SITE_LINK') === false) {
            $link = $urlParts['scheme'] . '://' . $urlParts['host'];
            $link .= SITE_FOLDER;
            define('SITE_LINK', $link);
        }
        $this->values['SITE_LINK'] = SITE_LINK;

        $this->values['SITE_STATE'] = defined('BACKEND_SITE_STATE') ? BACKEND_SITE_STATE : 'Unknown';
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
        $response = parent::transform($result);
        if (!($this->render instanceof RenderInterface)) {
            return $response;
        }

        //Add Headers
        $response->addHeader('Content-Type', 'text/html; charset=utf-8');

        //Transform the Body
        $body = $response->getBody();
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
        $this->values['buffered'] = '';
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
        }
        return $this->render->file($template, $values);
    }
}
