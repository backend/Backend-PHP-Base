<?php
namespace Backend\Base\Bindings;
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/share/php/libzend-framework-php');
require_once 'Zend/Loader.php';
\Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
\Zend_Loader::loadClass('Zend_Gdata_Gapps');
use Backend\Core\Utilities\ServiceLocator;
use Backend\Core\Exceptions\BackendException;
class GdataBinding extends Binding
{
    protected $key;

    protected $secret;

    protected $client;

    /**
     * The constructor for the object.
     *
     * @param array $settings The settings for the Service Connection
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $connection = $settings['connection'];
        $config = ServiceLocator::get('backend.Config');
        $settings = $config->get('remote_service', $connection);
        if (empty($settings['username']) || empty($settings['password'])) {
            throw new BackendException('Missing Google Admin User Credentials');
        }
        if (empty($settings['domain'])) {
            throw new BackendException('Missing Google Domain');
        }

        // Get the Client
        $httpClient = \Zend_Gdata_ClientLogin::getHttpClient($settings['username'], $settings['password'], \Zend_Gdata_Gapps::AUTH_SERVICE_NAME);
        $this->client = new \Zend_Gdata_Gapps($httpClient, $settings['domain']);
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
    public function find(array $conditions = array(), array $options = array())
    {
        $result = $this->client->retrieveAllUsers();
        if (!$result) {
            return false;
        }
        $result = iterator_to_array($result);
        $result = array_map(array($this, 'mapObjectToModel'), $result);
        return $result;
    }

    protected function mapObjectToModel(\Zend_Gdata_Gapps_UserEntry $object)
    {
        $name  = $object->getName();
        $login = $object->getLogin();
        $properties  = array(
            'given_name'  => $name->getGivenName(),
            'family_name' => $name->getFamilyName(),
            'username'    => $login->getUsername(),
            'password'    => $login->getPassword(),
            'admin'       => $login->getAdmin(),
            'active'      => !(boolean)$login->getSuspended(),
        );
        $object = new $this->className;
        $object->populate($properties);
        return $object;
    }

    /**
     * Create an instance on the source, and return the instance.
     *
     * @param array $data The data to create a new resource.
     *
     * @return \Backend\Core\Interfaces\ModelInterface The created model.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be created.
     */
    public function create(array $data)
    {
        try {
            $result = $this->client->createUser(
                $data['username'],
                $data['given_name'],
                $data['family_name'],
                $data['password']
            );
        } catch (\Zend_Gdata_Gapps_ServiceException $e) {
            //TODO Wrap this in an exception understood by Backend
            return false;
        }

        return $this->mapObjectToModel($result);
    }

    /**
     * Read and return the single, specified instance of the resource.
     *
     * @param mixed $identifier The unique identifier for the instance, or an
     * array containing criteria on which to search for the resource.
     *
     * @return \Backend\Core\Interfaces\ModelInterface The identified model.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be found.
     */
    public function read($identifier)
    {
        try {
            $result = $this->client->retrieveUser($identifier);
        } catch (\Exception $e) {
            //TODO Wrap this in an exception understood by Backend
            return false;
        }
        return $this->mapObjectToModel($result);
    }

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
    public function refresh(\Backend\Core\Interfaces\ModelInterface &$model)
    {
    }

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
    public function update(\Backend\Core\Interfaces\ModelInterface &$model)
    {
    }

    /**
     * Delete the specified instance of the resource
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to delete
     *
     * @return boolean If the deletion was succesful or not.
     * @throws \Backend\Core\Exceptions\BackendException When the resource can't be deleted.
     */
    public function delete(\Backend\Core\Interfaces\ModelInterface &$model)
    {
    }
}
