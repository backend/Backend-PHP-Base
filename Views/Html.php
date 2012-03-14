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
use \Backend\Core\Request;
use \Backend\Core\Response;
use \Backend\Core\Application;
use \Backend\Core\Decorators\PrettyExceptionDecorator;
use \Backend\Base\Utilities\Renderable;
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
     * The constructor for the object
     *
     * @param Request $request The Request to associate with the view
     */
    function __construct(Request $request)
    {
        ob_start();

        parent::__construct($request);

        //Get configured values
        $config = Application::getTool('Config');
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

        define('SITE_SUB_FOLDER', $urlParts['path']);
        $this->values['SITE_SUB_FOLDER'] = SITE_SUB_FOLDER;

        define('SITE_DOMAIN', $urlParts['host']);
        $this->values['SITE_DOMAIN'] = SITE_DOMAIN;

        define('SITE_PATH', $this->request->getSitePath());
        $this->values['SITE_PATH'] = SITE_PATH;

        define('SITE_LINK', $this->request->getSiteUrl());
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
        if ($result instanceof Response) {
            $response = $result;
            $body     = $response->getBody();
        } else {
            $response = new Response();
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
            $body = Application::getTool('Render')
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
            $values['title']     = get_class($object) . ' - ' . substr($object->getMessage(), 0, 100);
            $values['exception'] = new PrettyExceptionDecorator($object);
            break;
        }
        return Application::getTool('Render')
            ->file($template, $values);
    }
}
