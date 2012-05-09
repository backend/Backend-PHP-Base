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
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to create
     *
     * @return \Backend\Core\Interfaces\ModelInterface The created model if successful.
     */
    abstract public function create(\Backend\Core\Interfaces\ModelInterface $model);

    /**
     * Read a specified instance of the source, and return the instance
     *
     * @param mixed $identifier The unique identifier for the instance.
     *
     * @return \Backend\Core\Interfaces\ModelInterface The identified model if successful.
     */
    abstract public function read($identifier);

    /**
     * Update the specified instance of the resource
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to update
     *
     * @return \Backend\Core\Interfaces\ModelInterface The updated model if successful.
     */
    abstract public function update(\Backend\Core\Interfaces\ModelInterface $model);

    /**
     * Delete the specified instance of the resource
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to delete
     *
     * @return boolean If the deletion was succesful or not.
     */
    abstract public function delete(\Backend\Core\Interfaces\ModelInterface $model);
}
