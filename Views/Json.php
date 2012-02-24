<?php
/**
 * File defining \Base\Views\Json
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
namespace Backend\Base\Views;
use \Backend\Core\Decorators\JsonDecorator, \Backend\Core\Interfaces\DecorableInterface;
/**
 * Output a request in JavaScript Object Notation
 *
 * @package Views
 */
class Json extends \Backend\Core\View
{
    /**
     * Handle JSON requests
     * @var array
     */
    public static $handledFormats = array('json', 'text/json', 'application/json');

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

        if ($body instanceof DecorableInterface) {
            $body = new JsonDecorator($body);
            $body = $body->_toJson();
        } else {
            $body = json_encode($body);
        }
        $response->setBody($body);
        return $response;
    }
}
