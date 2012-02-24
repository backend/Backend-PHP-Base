<?php
/**
 * File defining \Base\BoundModel
 *
 * Copyright (c) 2011 JadeIT cc
 * @license http://www.opensource.org/licenses/mit-license.php
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software,
 * and to permit persons to whom the Software is furnished to do so, subject to the
 * following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR
 * A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package ModelFiles
 */
/**
 * Abstract class for models that are bound to a specific source
 *
 * @todo Define behaviour when the _identifier is changed
 * @todo Enable custom identifiers, or _identifier bound to a normal property
 * @package Models
 */
namespace Backend\Base\Models;
use \Backend\Base\Utilities\BindingFactory;

class BoundModel extends \Backend\Core\Model //implements \Backend\Core\Interfaces\RestModel
{
    /**
     * @var boolean Property to show if the model has changed since it's last commit / read
     */
    protected $_changed = false;

    /**
     * @var mixed The identifier for the model
     */
    protected $_identifier = null;

    /**
     * @var Binding The binding for the model
     */
    protected $_binding = null;

    /**
     * The constructor for the class
     *
     * @param mixed The identifier for the model
     * @param Binding The source for the model
     */
    public function __construct($identifier = null, \Backend\Base\Bindings\Binding $binding = null)
    {
        if (is_null($binding)) {
            $binding = $this->getBinding();
        }
        $this->_binding    = $binding;
        $this->_identifier = $identifier;
        if ($this->_identifier) {
            $this->read();
            $this->_changed = false;
        }
        $this->_decorators[] = '\Backend\Core\Decorators\JsonDecorator';
    }

    public function __set($propertyName, $value)
    {
        $result = parent::__set($propertyName, $value);
        $this->_changed = true;
        return $result;
    }

    public function populate(array $properties)
    {
        $result = parent::populate($properties);
        $this->_changed = true;
        return $result;
    }

    public static function create(array $data)
    {
        //Bit of a hack to make this static
        $className = get_called_Class();
        $object    = new $className();
        $object->populate($data);
        return $object->update();
    }

    public function read()
    {
        if (!$this->_identifier)
        {
            throw new \Exception('Cannot load unidentifier Bound Model');
        }
        $binding = $this->getBinding();
        $data    = $binding->read($this->_identifier);
        return $this->populate($data);
    }

    /**
     * Update the Bound Model on it's source.
     *
     * Bound Models aren't persisted on their source until the commit function is called
     */
    public function update()
    {
        if (!$this->_changed) {
            return $this;
        }
        $binding = $this->getBinding();
        if ($this->_identifier) {
            $binding->update($this->_identifier, $this->getProperties());
        } else {
            $data = $binding->create($this->getProperties());
            $this->_identifier = $data['id'];
        }
        $this->_changed = false;
        return $this;
    }

    public function destroy()
    {
        $binding = $this->getBinding();
        return $binding->delete($this->_identifier);
    }

    public static function findAll()
    {
        //Bit of a hack to make this static
        $className = get_called_Class();
        $object    = new $className();
        $binding   = $object->getBinding();
        return $binding->find();
    }

    public function getBinding()
    {
        if (!$this->_binding) {
            $this->_binding = BindingFactory::build(get_called_class());
        }
        return $this->_binding;
    }
}
