<?php
namespace Backend\Base\Views;
/**
 * File defining \Base\Views\Html
 *
 * Copyright (c) 2011 JadeIT cc
 * @license http://www.opensource.org/licenses/mit-license.php
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the
 * following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR
 * A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package ViewFiles
 */
/**
 * Output a request as HTML.
 *
 * @package Views
 */
class Html extends \Backend\Core\View
{
    /**
     * Handle HTML requests
     * @var array
     */
    public static $handledFormats = array('html', 'htm', 'text/html', 'application/xhtml+xml');

    /**
     * @var array An array of commonly used values
     */
    protected $_values = array();

    function __construct()
    {
        ob_start();

        self::setupConstants();

        parent::__construct();
    }

    /**
     * Set up a number of constants / variables to make creating and parsing templates easier.
     */
    private function setupConstants()
    {
        //Get the current URL
        $url = 'http';
        if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')) {
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
        $this->_values['SITE_SUB_FOLDER'] = SITE_SUB_FOLDER;

        //Parse the current URL to get the SITE_DOMAIN
        $urlParts = parse_url($url);
        $domain = !empty($urlParts['host']) ? $urlParts['host'] : 'localhost';
        define('SITE_DOMAIN', $domain);
        $this->_values['SITE_DOMAIN'] = SITE_DOMAIN;

        //Use SITE_DOMAIN and SITE_SUB_FOLDER to create a SITE_LINK
        $scheme = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url = SITE_DOMAIN . SITE_SUB_FOLDER;
        define('SITE_LINK', $scheme . $url);
        $this->_values['SITE_LINK'] = SITE_LINK;
    }

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

    protected function transformBody($body)
    {
        $this->_values['buffered'] = ob_get_clean();
        //Check for an Object
        if (is_object($body)) {
            $body = $this->transformObject($body);
        } else {
            if (is_array($body)) {
                $body = var_export($body, true);
            }
            $this->_values['content'] = $body;
            $body = \Backend\Core\Application::getTool('Render')->file('index', $this->_values);
        }
        return $body;
    }

    protected function transformObject($object)
    {
        $template = 'base.html.twig';
        $values   = $this->_values;
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
        return \Backend\Core\Application::getTool('Render')->file($template, $values);
    }
}
