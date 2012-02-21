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
 * @package Models
 */
namespace Backend\Base\Models;
use \Backend\Base\Utilities\BindingFactory;

abstract class BoundModel extends \Backend\Core\Model //implements \Backend\Core\Interfaces\RestModel
{
    /**
     * @var Binding The binding for the model
     */
    protected static $_binding = null;

    /**
     * The constructor for the class
     *
     * @param Binding The source for the model
     */
    public function __construct($id, \Backend\Base\Bindings\Binding $binding = null)
    {
        if (is_null($binding)) {
            $binding = call_user_func(array(get_called_class(), 'getBinding'));
        }
        self::$_binding = $binding;
    }

    public static function getBinding()
    {
        if (!self::$_binding) {
            self::$_binding = BindingFactory::build(get_called_class());
        }
        return self::$_binding;
    }

    public static function findAll()
    {
        $binding = self::getBinding();
        return $binding->find();
    }
}
