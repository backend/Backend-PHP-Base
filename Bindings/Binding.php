<?php
namespace Backend\Base\Bindings;
/**
 * File defining \Base\Binding
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
 * @package BindingFiles
 */
/**
 * Abstract class for Bindings
 *
 * Bindings act as a transport layer which can be used to perform CRUD actions on a resource.
 * It's typically used by Models to maintain their state on an outside resource.
 *
 * @package Bindings
 */
abstract class Binding
{
    /**
     * Find a specified instances of the resource
     *
     * Don't specify any criteria to retrieve a full list of instances.
     *
     * @param array An array of conditions on which to filter the list
     * @param array An array of options
     * @return array An array of representations of the resource
     */
    abstract public function find(array $conditions = array(), array $options = array());

    /**
     * Create an instance of the source, and return the instance
     *
     * @param mixed A respresentation of the data with which to create the instance
     * @return mixed A respresentation of the created instance of the resource if succesful.
     */
    abstract public function create($data);

    /**
     * Read a specified instance of the source, and return the instance
     *
     * @param mixed The unique identifier for the instance.
     * @return mixed A respresentation of the specified instance of the resource.
     */
    abstract public function read($identifier);

    /**
     * Update the specified instance of the resource
     *
     * @param mixed The unique identifier for the instance.
     * @param mixed A respresentation of the data with which to update the instance
     * @return mixed A respresentation of the updated instance of the resource if succesful.
     */
    abstract public function update($identifier, $data);

    /**
     * Delete the specified instance of the resource
     *
     * @param mixed The unique identifier for the instance.
     * @return boolean If the deletion was succesful or not.
     */
    abstract public function delete($identifier);
}
