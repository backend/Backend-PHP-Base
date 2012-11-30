<?php
/**
 * File defining TestBinding.
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    CoreTests
 * @subpackage Auxiliary
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */

/**
 * Class to test the Binding Factory
 *
 * @category   Backend
 * @package    CoreTests
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class TestBinding implements \Backend\Interfaces\BindingInterface
{
    /**
     * The constructor for the object.
     *
     * @param array $connection The connection settings for the Binding
     *
     * @throws \Backend\Modules\Exception
     */
    public function __construct(array $connection)
    {

    }

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
    public function find(
        array $conditions = array(),
        array $options = array()
    ) {

    }

    /**
     * Create an instance on the source, and return the instance.
     *
     * @param array $data The data to create a new resource.
     *
     * @return \Backend\Interfaces\ModelInterface The created model.
     * @throws \Backend\Modules\Exception         When the resource can't be created.
     */
    public function create(array $data)
    {

    }

    /**
     * Read and return the single, specified instance of the resource.
     *
     * @param mixed $identifier The unique identifier for the instance, or an
     * array containing criteria on which to search for the resource.
     *
     * @return \Backend\Interfaces\ModelInterface The identified model.
     * @throws \Backend\Modules\Exception         When the resource can't be found.
     */
    public function read($identifier)
    {

    }

    /**
     * Refresh the specified instance on the source.
     *
     * This function is the logical counterpart to update, and receives data from
     * the source.
     *
     * @param \Backend\Interfaces\ModelInterface &$model The model to refresh.
     * Passed by reference.
     *
     * @return boolean                    If the refresh was successful or not.
     * @throws \Backend\Modules\Exception When the resource can't be refreshed.
     */
    public function refresh(\Backend\Interfaces\ModelInterface &$model)
    {

    }

    /**
     * Update the specified instance of the resource.
     *
     * This function is the logical counterpart to refresh, and sends data to
     * the source.
     *
     * @param \Backend\Interfaces\ModelInterface &$model The model to update.
     * Passed by reference.
     *
     * @return boolean                    If the update was successful or not.
     * @throws \Backend\Modules\Exception When the resource can't be updated.
     */
    public function update(\Backend\Interfaces\ModelInterface &$model)
    {

    }

    /**
     * Delete the specified instance of the resource
     *
     * @param \Backend\Interfaces\ModelInterface &$model The model to delete
     *
     * @return boolean                    If the deletion was succesful or not.
     * @throws \Backend\Modules\Exception When the resource can't be deleted.
     */
    public function delete(\Backend\Interfaces\ModelInterface &$model)
    {

    }
}
