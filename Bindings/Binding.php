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
     * Find multiple instances of the resource.
     *
     * Don't specify any criteria to retrieve a full list of instances.
     *
     * @param array $conditions An array of conditions on which to filter the list.
     * @param array $options    An array of options.
     *
     * @return array An array of representations of the resource.
     */
    abstract public function find(array $conditions = array(), array $options = array());

    /**
     * Create an instance on the source, and return the instance.
     *
     * @param array $data The data to create a new resource.
     *
     * @return \Backend\Core\Interfaces\ModelInterface The created model.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be created.
     */
    abstract public function create(array $data);

    /**
     * Read and return the single, specified instance of the resource.
     *
     * @param mixed $identifier The unique identifier for the instance, or an
     * array containing criteria on which to search for the resource.
     *
     * @return \Backend\Core\Interfaces\ModelInterface The identified model.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be found.
     */
    abstract public function read($identifier);

    /**
     * Refresh the specified instance on the source.
     *
     * This function is the logical counterpart to update, and receives data from the source.
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to refresh.
     * Passed by reference.
     *
     * @returns boolean If the refresh was successful or not.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be refreshed.
     */
    abstract public function refresh(\Backend\Core\Interfaces\ModelInterface &$model);

    /**
     * Update the specified instance of the resource.
     *
     * This function is the logical counterpart to refresh, and sends data to the source.
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to update.
     * Passed by reference.
     *
     * @returns boolean If the update was successful or not.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be updated.
     */
    abstract public function update(\Backend\Core\Interfaces\ModelInterface &$model);

    /**
     * Delete the specified instance of the resource
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to delete
     *
     * @return boolean If the deletion was succesful or not.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be deleted.
     */
    abstract public function delete(\Backend\Core\Interfaces\ModelInterface &$model);
}
