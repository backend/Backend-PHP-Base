<?php
/**
 * File defining \Base\Views\Html
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Views
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Views;
/**
 * Output a request as HTML.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Views
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Html extends \Backend\Core\View
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
     * Constructor for the object
     */
    function __construct()
    {
        ob_start();

        self::_setupConstants();

        parent::__construct();
    }

    /**
     * Set up a number of constants / variables to make creating and parsing
     * templates easier.
     *
     * @return null
     */
    private function _setupConstants()
    {
        //Get the current URL
        $url = 'http';
        if ($_SERVER['SERVER_PORT'] == 443
            || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')
        ) {
            $url .= 's';
        }
        $url .= '://' . $_SERVER['HTTP_HOST'];

        $url .= $_SERVER['PHP_SELF'];
        if (!empty($_SERVER['QUERY_STRING'])) {
            $url .= '?' . $_SERVER['QUERY_STRING'];
        }

        if (array_key_exists('SCRIPT_NAME', $_SERVER)) {
            $folder = $_SERVER['SCRIPT_NAME'];
        } else {
            //TODO:
        }
        $folder = preg_replace('/\/index.php.*/', '/', $folder);
        if (substr($folder, -1) != '/') {
            $folder .= '/';
        }

        define('SITE_SUB_FOLDER', $folder);
        $this->values['SITE_SUB_FOLDER'] = SITE_SUB_FOLDER;

        //Parse the current URL to get the SITE_DOMAIN
        $urlParts = parse_url($url);
        $domain = !empty($urlParts['host']) ? $urlParts['host'] : 'localhost';
        define('SITE_DOMAIN', $domain);
        $this->values['SITE_DOMAIN'] = SITE_DOMAIN;

        //Use SITE_DOMAIN and SITE_SUB_FOLDER to create a SITE_LINK
        $scheme = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url = SITE_DOMAIN . SITE_SUB_FOLDER;
        define('SITE_LINK', $scheme . $url);
        $this->values['SITE_LINK'] = SITE_LINK;
    }

    /**
     * Transform the result into a Response Object containing the result as HTML
     *
     * @param mixed $result The result to transform
     *
     * @return Response The result transformed into a HTML Response
     * @todo Add HTML related headers such as content type and encoding
     */
    public function transform($result)
    {
        if ($result instanceof \Backend\Core\Response) {
            $response = $result;
            $body     = $response->getBody();
        } else {
            $response = new \Backend\Core\Response();
            $body     = $result;
        }
        $response->addHeader('X-Backend-View', get_class($this));

        if (!is_string($body)) {
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
        $this->values['buffered'] = ob_get_clean();
        //Check for an Object
        if (is_object($body)) {
            $body = $this->transformObject($body);
        } else {
            if (is_array($body)) {
                $body = var_export($body, true);
            }
            $this->values['content'] = $body;
            $body = \Backend\Core\Application::getTool('Render')
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
        $template = 'base.html.twig';
        $values   = $this->values;
        switch (true) {
        case $object instanceof \Renderable:
            $template = $object->getTemplate();
            $values   = array_merge($values, $object->getValues());
            break;
        case $object instanceof \Exception:
            $template            = 'exception';
            $values['title']     = 'Exception: ' . get_class($object);
            $values['exception'] = $object;
            break;
        }
        return \Backend\Core\Application::getTool('Render')
            ->file($template, $values);
    }
}
