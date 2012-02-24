<?php
namespace Backend\Base\Views;
/**
 * File defining \Base\Views\Cli
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
 * Output a request on the Command Line.
 *
 * @package Views
 */
class Cli extends \Backend\Core\View
{
    /**
     * Handle CLI requests
     * @var array
     */
    public static $handledFormats = array('cli');

    function transform($result)
    {
        $result = 'Result:' . PHP_EOL;
        $result .= var_export($result, $true);
        $result .= PHP_EOL;
        $response = new \Backend\Core\Response($result, 200);
        $response->addHeader('X-Backend-View', get_class($this));
        return $response;
    }
}
