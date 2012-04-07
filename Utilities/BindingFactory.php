<?php
/**
 * File defining BindingFactory
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Utilities;
use \Backend\Core\Utilities\Strings;
use \Backend\Core\Application;
/**
 * Factory class to create Bindings
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class BindingFactory
{
    /**
     * Build the binding using  the specified model name
     *
     * @param mixed $modelName The name of the model or the model for which to buld
     * the binding
     *
     * @return Binding The binding
     */
    public static function build($modelName)
    {
        $modelName = is_object($modelName) ? get_class($modelName) : $modelName;
        $fileName = PROJECT_FOLDER . 'configs/bindings.yaml';
        $bindings = new \Backend\Core\Utilities\Config($fileName);

        if (!($binding = $bindings->get($modelName))) {
            throw new \Exception('No binding setup for ' . $modelName);
        }
        if (empty($binding['type'])) {
            throw new \Exception('Missing Type for Binding for ' . $modelName);
        }

        $bindingClass = $binding['type'];
        unset($binding['type']);

        if (empty($binding['connection'])) {
            $binding['connection'] = 'default';
        }

        $bindingObj = new $bindingClass($binding);
        return $bindingObj;

        $connection = empty($binding['connection']) ? 'default'                      : $binding['connection'];
        $table      = empty($binding['table'])      ? Strings::tableName($modelName) : $binding['table'];

        switch (true) {
        case is_subclass_of($binding['type'], '\Backend\Base\Bindings\DatabaseBinding'):
            $binding = new $binding['type']($connection, $table);
            break;
        case is_subclass_of($binding['type'], '\Backend\Base\Bindings\URLBinding'):
            throw new \Exception('Unimplemented');
            break;
        default:
            throw new \Exception('Unknown Binding Type: ' . $binding['type']);
            break;
        }
        return $binding;
    }
}
