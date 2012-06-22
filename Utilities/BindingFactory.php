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
use \Backend\Interfaces\BindingFactoryInterface;
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
class BindingFactory implements BindingFactoryInterface
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
        $fileName  = PROJECT_FOLDER . 'configs/bindings.yaml';
        $bindings  = new \Backend\Core\Utilities\Config($fileName);

        if (!($binding = $bindings->get($modelName))) {
            throw new \Exception('No binding setup for ' . $modelName);
        }
        if (empty($binding['type'])) {
            throw new \Exception('Missing Type for Binding for ' . $modelName);
        }

        $bindingClass = $binding['type'];
        unset($binding['type']);

        //Use the Class we're checking for
        if (empty($binding['class'])) {
            $binding['class'] = $modelName;
        }
        //Use the Default Connection
        if (empty($binding['connection'])) {
            $binding['connection'] = 'default';
        }

        $bindingObj = new $bindingClass($binding);
        return $bindingObj;
    }
}
