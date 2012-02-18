<?php
namespace Backend\Base\Utilities;
/**
 * File defining Base\Utilities\Renderable
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
 * @package UtilityFiles
 */
/**
 * An object that can be Rendered by the Render Utility
 *
 * Return a Renderable object from a Controller if you want to specify what template to render
 *
 * @package Utility
 */
class Renderable
{
    
    protected $_template = 'index';
    
    protected $_values = array();

    function __construct($template, array $values = array())
    {
        $this->_template = $template;
        $this->_values = $values;
    }
    
    public function getTemplate()
    {
        return $this->_template;
    }
    
    public function setTemplate($template)
    {
        $this->_template = $template;
    }
    
    public function getValues()
    {
        return $this->_values;
    }
    
    public function setValue($name, $value)
    {
        $this->_values[$name] = $value;
    }
    
    public function setValues(array $values)
    {
        $this->_values = $values;
    }
    
    public function __toString()
    {
        return \Backend\Core\Application::getTool('Render')->file(
            $this->_template,
            $this->_values
        );
    }
}
