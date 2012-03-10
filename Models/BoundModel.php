<?php
/**
 * File defining \Base\BoundModel
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Models
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Models;
use \Backend\Base\Utilities\BindingFactory;
use \Backend\Base\Bindings\Binding;
/**
 * Class for models that are bound to a specific source
 *
 * @category   Backend
 * @package    Base
 * @subpackage Models
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 * @todo Enable custom identifiers
 */
class BoundModel extends \Backend\Core\Model //implements \Backend\Core\Interfaces\RestModel
{
    /**
     * @var boolean Property to show if the Model has changed since it's last update / read
     */
    private $_changed = false;

    /**
     * @var \Backend\Base\Bindings\Binding The binding for the Model
     */
    private $_binding = null;

    /**
     * @var mixed The identifier for the Model
     */
    protected $id = null;

    /**
     * The constructor for the class
     *
     * @param mixed   $id      The identifier for the Model
     * @param Binding $binding The source for the Model
     */
    public function __construct($id = null, Binding $binding = null)
    {
        if (is_null($binding)) {
            $binding = $this->getBinding();
        }
        $this->_binding = $binding;
        $this->setId($id);
        $this->addDecorator('\Backend\Core\Decorators\JsonDecorator');
    }

    /**
     * Magic __set function
     *
     * @param string $propertyName The name of the property being set
     * @param mixed  $value        The value of the property being set
     *
     * @return BoundModel The current Model
     */
    public function __set($propertyName, $value)
    {
        $result = parent::__set($propertyName, $value);
        $this->setChanged(true);
        return $result;
    }

    /**
     * Get the identifier for the Model
     *
     * @return mixed The identifier for the Model
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the identifier for the Model
     *
     * This will trigger a refresh of the model
     *
     * @param mixed $id The identifier for the Model
     *
     * @return BoundModel The current Model
     */
    public function setId($id)
    {
        //Don't set the ID and read if it's the same as the current ID
        if ($id != $this->id) {
            $this->id = $id;
            if (!$this->read()) {
                //Set the id to null if the read is unsuccesful
                $this->id = null;
            }
        }
        return $this;
    }

    /**
     * Populate the Model with the specified properties.
     *
     * The function will use any `set` functions defined.
     *
     * @param array $properties An array containing the properties for the model
     *
     * @return Object The object that was populated
     */
    public function populate(array $properties)
    {
        $result = parent::populate($properties);
        $this->setChanged(true);
        return $result;
    }

    /**
     * Create the Model on it's source
     *
     * @param array $data The data that should be used to create the Model
     *
     * @return BoundModel The current Model
     */
    public static function create(array $data)
    {
        //Bit of a hack to make this static
        $className = get_called_class();
        $object    = new $className();
        $object->populate($data);
        return $object->update();
    }

    /**
     * Populate the Model by reading from it's source
     *
     * @return BoundModel The current Model
     */
    public function read()
    {
        if (!$this->id) {
            throw new \Exception('Cannot load unidentified Bound Model');
        }
        $binding = $this->getBinding();
        if ($data = $binding->read($this->id)) {
            return $this->populate($data);
        } else {
            return false;
        }
    }

    /**
     * Update the Bound Model on it's source.
     *
     * If no changes were made to the model, no update call will be made to the Binding
     *
     * @return BoundModel The current Model
     */
    public function update()
    {
        if (!$this->getChanged()) {
            return $this;
        }
        $binding = $this->getBinding();
        if ($this->id) {
            $binding->update($this->id, $this->getProperties());
        } else {
            $data = $binding->create($this->getProperties());
            $this->id = $data['id'];
        }
        $this->setChanged(false);
        return $this;
    }

    /**
     * Destroy the Bound Model on it's source.
     *
     * @return boolean If the Model was succesfully destroyed or not
     */
    public function destroy()
    {
        if (!$this->id) {
            throw new \Exception('Cannot load unidentified Bound Model');
        }
        $binding = $this->getBinding();
        return $binding->delete($this->id);
    }

    /**
     * Get a list of all the representations of the Model
     *
     * @return array An array of representations of the Model
     */
    public static function findAll()
    {
        //Bit of a hack to make this static
        $className = get_called_Class();
        $object    = new $className();
        $binding   = $object->getBinding();
        return $binding->find();
    }

    /**
     * Get the Bound Model's Binding
     *
     * @return \Backend\Base\Bindings\Binding The Bound Model's Binding
     */
    public function getBinding()
    {
        if (!$this->_binding) {
            $this->_binding = BindingFactory::build(get_called_class());
        }
        return $this->_binding;
    }

    /**
     * Set the Bound Model's Binding
     *
     * @param \Backend\Base\Bindings\Binding $binding The Bound Model's Binding
     *
     * @return null;
     */
    public function setBinding(Binding $binding)
    {
        $this->_binding = $binding;
    }

    /**
     * Get the Bound Model's Changed state
     *
     * @return boolean If the Bound Model was changed since it's last sync / update
     */
    public function getChanged()
    {
        return $this->_changed;
    }

    /**
     * Set the Bound Model's Changed state
     *
     * @param boolean $changed The new changed state for the Bound Model
     *
     * @return null
     */
    public function setChanged($changed)
    {
        $this->_changed = $changed;
    }
}
