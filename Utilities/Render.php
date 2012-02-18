<?php
namespace Backend\Base\Utilities;
/**
 * File defining Base\Utilities\Render
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
 * Render templates
 *
 * @package Utility
 */
class Render implements \Backend\Base\Interfaces\RenderUtility
{
    /**
     * Location for template files. List them in order of preference
     * @var array
     */
    protected $_templateLocations = array();

    /**
     * This contains the variables bound to the renderer
     * @var array
     */
    protected $_variables = array();

    public function __construct()
    {
        $this->_templateLocations = array();
        //Check Application Folder
        $this->_templateLocations += glob(SOURCE_FOLDER . '*/*/templates/', \GLOB_ONLYDIR);

        //Add Project wide templates
        $this->_templateLocations[] = PROJECT_FOLDER . 'templates/';

        //Check Vendor Folder
        $this->_templateLocations += glob(VENDOR_FOLDER . '*/*/templates/', \GLOB_ONLYDIR);

        $this->_templateLocations = array_filter($this->_templateLocations, 'file_exists');
    }

    /**
     * Bind a variable to the renderer
     *
     * @param string The name of the variable
     * @param mixed The value of the variable
     * @param boolean Set to false to honor previously set values
     */
    public function bind($name, $value, $overwrite = true)
    {
        if ($overwrite || !array_key_exists($name, $this->_variables)) {
            $this->_variables[$name] = $value;
        }
        return $this->_variables[$name];
    }

    /**
     * Get the value of a variable
     *
     * @param string The name of the variable
     * @return mixed The value of the variable
     */
    public function get($name)
    {
        return array_key_exists($name, $this->_variables) ? $this->_variables[$name] : null;
    }

    /**
     * Get all of the bound variables
     *
     * @return array An array of all the variables bound to the renderer
     */
    public function getVariables()
    {
        return $this->_variables;
    }

    public function file($template, array $r_values = array())
    {
        $r_file = $this->templateFile($template);
        if (!$r_file) {
            //TODO Throw an exception, make a fuss?
            \Backend\Core\Application::log('Missing Template: ' . $template, 4);
            return false;
        }

        //TODO Add Caching
        ob_start();
        extract($r_values);
        include($r_file);
        $result = ob_get_clean();

        //Substitute Variables into the templates
        $result = $this->parseVariables($result, $r_values);

        return $result;
    }

    /**
     * Get the file name for the specified template
     *
     * @param string The name of the template
     * @return string The absolute path to the template file to render
     */
    protected function templateFile($template)
    {
        $template = $this->templateFileName($template);
        $locations = array();
        if (!empty($this->_templateLocations) && is_array($this->_templateLocations)) {
            $locations = array_merge($locations, $this->_templateLocations);
        }
        foreach (array_unique($locations) as $location) {
            if (file_exists($location . $template)) {
                return $location . $template;
            }
        }
        return false;
    }
    
    protected function templateFileName($template)
    {
        if (substr($template, -8) != '.tpl.php') {
            $template .= '.tpl.php';
        }
        return $template;
    }

    /**
     * Check the string for variables (#VarName#) and replace them with the appropriate values
     *
     * The values currently bound to the view will be used.
     *
     * @param string The string to check for variable names
     * @param array Extra variables to consider
     * @return string The string with the variables replaced
     */
    function parseVariables($string, array $values = array())
    {
        foreach ($values as $name => $value) {
            if (is_string($name) && is_string($value)) {
                $search[] = '#' . $name . '#';
                $replace[] = $value;
            }
        }
        $string = str_replace($search, $replace, $string);
        return $string;
    }
}