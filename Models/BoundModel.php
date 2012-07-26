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
 * @todo Do a Name field, with the ability to specify the name field. It will act
 * as a readable field, so you can do <a href="{{model}}/{{model.id}}">{{model.name}}</a>
 */
class BoundModel extends \Backend\Core\Model
{
    /**
     * @var boolean Property to show if the Model has changed since it's last update / read
     */
    private $_changed = false;

    /**
     * @var \Backend\Base\Bindings\Binding The binding for the Model
     */
    protected $_binding = null;

    /**
     * @var mixed The identifier for the Model
     */
    protected $id = null;

    /**
     * The constructor for the class.
     *
     * This should rarely be used, rather use the static create function.
     *
     * @param mixed $id The identifier for the Model
     */
    public function __construct($id = null)
    {
        $this->setId($id);
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
        $className = get_called_class();
        $object    = new $className();
        $binding   = $object->getBinding();
        return $binding->create($data);
    }

    /**
     * Populate the Model by reading from it's source
     *
     * @param mixed $identifier The unique identifier for the instance, or an
     * array containing criteria on which to search for the resource.
     *
     * @return BoundModel The current Model
     */
    public static function read($identifier)
    {
        $className = get_called_class();
        $object    = new $className();
        $binding   = $object->getBinding();
        if ($model = $binding->read($identifier)) {
            return $model;
        }
        return null;
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
        if (!$this->hasChanged()) {
            return $this;
        }
        $binding = $this->getBinding();
        $binding->update($this);
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
        return $binding->delete($this);
    }

    /**
     * Get a list of all the representations of the Model
     *
     * @param array $options Options used to affect the records returned.
     *
     * @return array An array of representations of the Model
     */
    public static function findAll(array $options = array())
    {
        $defaults = array(
            'order'     => false,
            'direction' => 'ASC',
        );
        $options = $options + $defaults;
        $binding = BindingFactory::build(get_called_class());
        return $binding->find(array(), $options);
    }

    public static function __callStatic($method, $args)
    {
        $class = get_called_class();
        $object = new $class();
        $binding = $object->getBinding();
        if (is_callable(array($binding, $method))) {
            return $binding->$method($args);
        }
        throw new \Exception('Unimplemented Static Function: ' . __CLASS__ . '::' . $method);
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
    public function hasChanged()
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
