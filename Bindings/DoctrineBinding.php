<?php
/**
 * File defining DoctrineBinding
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Bindings;
use Backend\Core\Application;
use Doctrine\ORM\EntityManager;
/**
 * Binding for Doctrine connections.
 *
 * This class assumes that you installed Doctrine using PEAR.
 *     pear channel-discover pear.doctrine-project.org
 *     pear channel-discover pear.symfony.com
 *     pear install --alldeps doctrine/DoctrineORM
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 * @todo This is a rudimentary implementation of the PDOBinding. It can be improved a lot.
 */
class DoctrineBinding extends DatabaseBinding
{
    protected $em;

    protected $entityName;

    /**
     * The constructor for the object.
     *
     * The settings array must contain at least the name of the entity to bind to.
     *
     * @param array $settings The settings for the Doctrine Binding
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $this->entityName = $settings['class'];
    }

    /**
     * Initialize the connection
     *
     * @param array $connection The connection information for the binding
     *
     * @return Object The current object 
     */
    protected function init(array $connection)
    {
        //Setup Doctrine
        include_once "Doctrine/ORM/Tools/Setup.php";
        \Doctrine\ORM\Tools\Setup::registerAutoloadPEAR();
        $isDevMode = (Application::getSiteState() != 'production');
        $config    = \Doctrine\ORM\Tools\Setup::createYAMLMetadataConfiguration(
            array(PROJECT_FOLDER . 'configs/doctrine'),
            $isDevMode
        );

        // obtaining the entity manager
        $this->em = EntityManager::create($connection, $config);
    }

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
    public function find(array $conditions = array(), array $options = array())
    {
        return $this->em->getRepository($this->entityName)->findAll();
    }

    /**
     * Create an instance of the source, and return the instance
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to create
     *
     * @return \Backend\Core\Interfaces\ModelInterface The created model if successful.
     */
    public function create(\Backend\Core\Interfaces\ModelInterface $model)
    {
        $this->em->persist($model);
        $this->em->flush();
        return $this->read($model->getId());
    }

    /**
     * Read a specified instance of the source, and return the instance
     *
     * @param mixed $identifier The unique identifier for the instance.
     *
     * @return \Backend\Core\Interfaces\ModelInterface The identified model if successful.
     */
    public function read($identifier)
    {
    }

    /**
     * Update the specified instance of the resource
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to update
     *
     * @return \Backend\Core\Interfaces\ModelInterface The updated model if successful.
     */
    public function update(\Backend\Core\Interfaces\ModelInterface $model)
    {
    }

    /**
     * Delete the specified instance of the resource
     *
     * @param \Backend\Core\Interfaces\ModelInterface $model The model to delete
     *
     * @return boolean If the deletion was succesful or not.
     */
    public function delete(\Backend\Core\Interfaces\ModelInterface $model)
    {
    }
}
