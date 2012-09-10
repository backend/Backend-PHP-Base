<?php
/**
 * File defining \Base\Model
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   Base
 * @author    J Jurgens du Toit <jrgns@jrgns.net>
 * @copyright 2011 - 2012 Jade IT (cc)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://backend-php.net
 */
namespace Backend\Base;
/**
 * The main Model class.
 *
 * Normal / bindable properties should NOT start with an underscore. Meta properties should.
 *
 * @category Backend
 * @package  Base
 * @author   J Jurgens du Toit <jrgns@jrgns.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class Model implements \Backend\Interfaces\ModelInterface
{
    /**
     * Magic __get function
     *
     * @param string $name The name of the property being retrieved
     *
     * @return mixed The value of the property
     */
    public function __get($name)
    {
        $funcName = new Utilities\String($name);
        $funcName = 'get' . $funcName->camelCase();
        if (method_exists($this, $funcName)) {
            return $this->$funcName();
        } else if (property_exists($this, $name)) {
            return $this->$name;
        }
        throw new \ErrorException('Undefined property: ' . __CLASS__ . '::$' . $name);
    }

    /**
     * Magic __set function
     *
     * @param string $name  The name of the property being set
     * @param mixed  $value The value of the property being set
     *
     * @return Model The current Model
     */
    public function __set($name, $value)
    {
        $funcName = new Utilities\String($name);
        $funcName = 'set' . $funcName->camelCase();
        if (method_exists($this, $funcName)) {
            $this->$funcName($value);
        } else if (property_exists($this, $name)) {
            $this->$name = $value;
        } else {
            throw new \ErrorException(
                'Trying to set Undefined property: ' . __CLASS__ . '::$' . $name
            );
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
        foreach ($properties as $name => $value) {
            $funcName = new Utilities\String($name);
            $funcName = 'set' . $funcName->camelCase();
            if (method_exists($this, $funcName)) {
                $this->$funcName($value);
            } else if (property_exists($this, $name)) {
                $this->$name = $value;
            } else if ($name[0] !== '_') {
                throw new \ErrorException(
                    'Undefined property: ' . __CLASS__ . '::$' . $name
                );
            }
        }
        return $this;
    }

    /**
     * Get the properties of the Model
     *
     * @return array The properties of the model as a key / value array
     */
    public function getProperties()
    {
        $reflector  = new \ReflectionClass($this);
        $properties = $reflector->getProperties(\ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED);
        $result     = array();
        foreach ($properties as $property) {
            if ($property->isStatic() || $property->isPrivate()
                || substr($property->getName(), 0, 1) == '_'
            ) {
                continue;
            }
            $result[$property->getName()] = $this->{$property->getName()};
        }
        return $result;
    }
}
