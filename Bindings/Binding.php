<?php
/**
 * File defining \Base\Binding
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   Base/Bindings
 * @author    J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright 2011 - 2012 Jade IT (cc)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://backend-php.net
 */
namespace Backend\Base\Bindings;
/**
 * Abstract class for Bindings
 *
 * Bindings act as a transport layer which can be used to perform CRUD actions
 * on a resource. It's typically used by Models to maintain their state on an
 * outside resource.
 *
 * @category Backend
 * @package  Base/Bindings
 * @author   J Jurgens du Toit <jrgns@jrgns.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
abstract class Binding
{
    /**
     * Find a specified instances of the resource
     *
     * Don't specify any criteria to retrieve a full list of instances.
     *
     * @param array $conditions An array of conditions on which to filter the list
     * @param array $options    An array of options
     *
     * @return array An array of representations of the resource
     */
    abstract public function find(array $conditions = array(), array $options = array());

    /**
     * Create an instance of the source, and return the instance
     *
     * @param mixed $data A respresentation of the data with which to create the instance
     *
     * @return mixed A respresentation of the created instance of the resource if succesful.
     */
    abstract public function create($data);

    /**
     * Read a specified instance of the source, and return the instance
     *
     * @param mixed $identifier The unique identifier for the instance.
     *
     * @return mixed A respresentation of the specified instance of the resource.
     */
    abstract public function read($identifier);

    /**
     * Update the specified instance of the resource
     *
     * @param mixed $identifier The unique identifier for the instance.
     * @param mixed $data       A respresentation of the data with which to update the instance
     *
     * @return mixed A respresentation of the updated instance of the resource if succesful.
     */
    abstract public function update($identifier, $data);

    /**
     * Delete the specified instance of the resource
     *
     * @param mixed $identifier The unique identifier for the instance.
     *
     * @return boolean If the deletion was succesful or not.
     */
    abstract public function delete($identifier);
}
