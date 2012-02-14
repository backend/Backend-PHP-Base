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
     * Location for template files. List them in order of preference
     * @var array
     */
    public $templateLocations = array();

    function __construct($renderer = null)
    {
        ob_start();

        self::setupConstants();

        $this->templateLocations = array(
            APP_FOLDER . 'templates/',
            BACKEND_FOLDER . 'templates/',
        );

        $this->templateLocations = array_filter($this->templateLocations, 'file_exists');

        parent::__construct($renderer);
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

        //Parse the current URL to get the SITE_SUB_FOLDER
        $url = parse_url($url);
        $folder = !empty($url['path']) ? $url['path'] : '/';
        if (substr($folder, -1) != '/' && substr($folder, -1) != '\\') {
            $folder = dirname($folder);
        }
        if ($folder != '.') {
            if (substr($folder, strlen($folder) - 1) != '/') {
                $folder .= '/';
            }
            define('SITE_SUB_FOLDER', $folder);
        } else {
            define('SITE_SUB_FOLDER', '/');
        }
        $this->bind('SITE_SUB_FOLDER', SITE_SUB_FOLDER);

        //Parse the current URL to get the SITE_DOMAIN
        $domain = !empty($url['host']) ? $url['host'] : 'localhost';
        define('SITE_DOMAIN', $domain);
        $this->bind('SITE_DOMAIN', SITE_DOMAIN);

        //Use SITE_DOMAIN and SITE_SUB_FOLDER to create a SITE_LINK
        $scheme = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $url = SITE_DOMAIN . SITE_SUB_FOLDER;
        define('SITE_LINK', $scheme . $url);
        $this->bind('SITE_LINK', SITE_LINK);
    }

    function output()
    {
        $result = $this->get('result');
        //Check for an exception
        if ($result instanceof \Exception) {
            $title   = 'Exception: ' . get_class($result);
            $content = $this->render('exception.tpl.php');
        } else {
            //Get a Title
            $title = $this->get('title');
            if (empty($title)) {
                if (is_object($result)) {
                    $title = get_class($result);
                } else if (is_array($result)) {
                    $title = 'Array(' . count($result) . ')';
                } else {
                    $title = (string)$result;
                }
                $this->bind('title', 'Result: ' . $title);
            }
            $content = $result;
        }
        $this->bind('content', $content);

        //Get buffered output
        $buffered = ob_get_clean();
        $this->bind('buffered', $buffered);

        echo $this->render('index.tpl.php');
    }
}
