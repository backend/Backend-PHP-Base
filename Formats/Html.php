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
use \Backend\Core\Request;
use \Backend\Core\Config;
use \Backend\Core\Utilities\ServiceLocator;
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
class Html extends \Backend\Core\Utilities\Format
{
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
     * @param \Backend\Core\Request $request The Request to associate with the view
     * @param \Backend\Core\Config $config Config object to use in setting up values
     */
    function __construct(Request $request, Config $config = null)
    {
        parent::__construct($request);

        //Get configured values
        $config = $config ?: ServiceLocator::get('backend.Config');
        $this->values = $config->get('application', 'values');

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
        $urlParts = parse_url($this->request->getSitePath());

        if (!defined('SITE_SUB_FOLDER')) {
            define('SITE_SUB_FOLDER', $urlParts['path']);
        }
        $this->values['SITE_SUB_FOLDER'] = SITE_SUB_FOLDER;

        if (!defined('SITE_DOMAIN')) {
            define('SITE_DOMAIN', $urlParts['host']);
        }
        $this->values['SITE_DOMAIN'] = SITE_DOMAIN;

        if (!defined('SITE_PATH')) {
            define('SITE_PATH', $this->request->getSitePath());
        }
        $this->values['SITE_PATH'] = SITE_PATH;

        if (!defined('SITE_LINK')) {
            define('SITE_LINK', $this->request->getSiteUrl());
        }
        $this->values['SITE_LINK'] = SITE_LINK;

        // TODO
        //$this->values['SITE_STATE'] = SITE_STATE;
    }

    /**
     * Transform the result into a Response Object containing the result as HTML
     *
     * @param mixed $result The result to transform into HTML
     *
     * @return \Backend\Core\Response The response transformed into a HTML Response
     * @todo Make the content type header customizable
     */
    public function transform($result, $callback, array $arguments)
    {
        $response = parent::transform($result, $callback, $arguments);
        //@todo Remove this dependency
        if (!ServiceLocator::has('backend.Render')) {
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
            $body = ServiceLocator::get('backend.Render')
                ->file('index', $this->values);
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
            if (property_exists('xdebug_message', $object)) {
                $values['xdebug_message'] = $object->xdebug_message;
            }
            $values['exception'] = new PrettyExceptionDecorator($object);
            break;
        }
        return ServiceLocator::get('backend.Render')->file($template, $values);
    }
}
