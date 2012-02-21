<?php
/**
 * File defining BindingFactory
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
 * @package UtilityFiles
 */
/**
 * Factory class to create Bindings
 *
 * @package Utility
 */
namespace Backend\Base\Utilities;
use \Backend\Core\Utilities\Strings;
use \Backend\Core\Application;

class BindingFactory
{
    public static function build($modelName)
    {
        $fileName = PROJECT_FOLDER . 'configs/bindings.yaml';
        $bindings = new \Backend\Core\Utilities\Config($fileName);

        if (!($binding = $bindings->get($modelName))) {
            throw new \Exception('No binding setup for ' . $modelName);
        }
        if (empty($binding['type'])) {
            throw new \Exception('Missing Type for Binding for ' . $modelName);
        }
        $connection = empty($binding['connection']) ? 'default'                      : $binding['connection'];
        $table      = empty($binding['table'])      ? Strings::tableName($modelName) : $binding['table'];

        switch (true) {
        case is_subclass_of($binding['type'], '\Backend\Base\Bindings\DatabaseBinding'):
            $binding = new $binding['type']($connection, $table);
            break;
        case is_subclass_of($binding['type'], 'URLBinding'):
            break;
        default:
            throw new \Exception('Unknown Binding Type: ' . $binding['type']);
            break;
        }
    }
}
