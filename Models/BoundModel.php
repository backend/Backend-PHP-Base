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
use Backend\Interfaces\ModelInterface;
use Backend\Interfaces\BindingFactoryInterface;
use Backend\Core\Utilities\Config;
use Backend\Base\Utilities\BindingFactory;
use Backend\Modules\Bindings\Binding;
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
 * as a readable field, so you can do
 * <a href="{{model}}/{{model.id}}">{{model.name}}</a>
 */
class BoundModel extends \Backend\Base\Model
{
    /**
     * The identifier for the Model
     *
     * @var mixed
     */
    protected $id = null;

    /**
     * The constructor for the class.
     *
     * This should rarely be used, rather use the Binding::create function.
     *
     * @param mixed $id The identifier for the Model.
     */
    public function __construct($id = null)
    {
        $this->setId($id);
        $this->factory = $factory;
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
        $binding = static::getBinding();
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
    public static function getBinding()
    {
        if (empty(static::$_binding)) {
            static::$_binding = static::getBindingFactory()->build(get_called_class());
        }
        return static::$_binding;
    }

    /**
     * Set the Bound Model's Binding
     *
     * @param \Backend\Base\Bindings\Binding $binding The Bound Model's Binding
     *
     * @return null;
     */
    public static function setBinding(Binding $binding)
    {
        static::$_binding = $binding;
    }
}
