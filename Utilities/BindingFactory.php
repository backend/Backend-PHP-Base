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
use Backend\Interfaces\BindingFactoryInterface;
use Backend\Interfaces\ConfigInterface;
use Backend\Core\Utilities\Strings;
use Backend\Core\Application;
use Backend\Core\Exceptions\ConfigException;
/**
 * Factory class to create Bindings
 *
 * @category   Backend
 * @package    Base
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 *
 * @todo Cache built bindings
 */
class BindingFactory implements BindingFactoryInterface
{
    /**
     * An array defining the available bindings.
     *
     * @var array
     */
    protected $bindings;

    /**
     * An array defining the available connections.
     *
     * @var array
     */
    protected $connections;

    /**
     * The class constructor.
     *
     * @param Backend\Interfaces\ConfigInterface|array $bindings    The bindings config as
     * a Config object or an array
     * @param Backend\Interfaces\ConfigInterface|array $connections The bindings config as
     * a Config object or an array
     */
    public function __construct($bindings, $connections)
    {
        // Setup Bindings
        if ($bindings instanceof ConfigInterface) {
            $bindings = $bindings->get();
        } else if (is_object($bindings)) {
            $bindings = (array)$bindings;
        } else if (is_array($bindings) === false) {
            throw new ConfigException('Invalid Bindings Configuration');
        }
        $this->bindings = $bindings;
        // Setup Connections
        if ($connections instanceof ConfigInterface) {
            $connections = $connections->get();
        } else if (is_object($connections)) {
            $connections = (array)$connections;
        } else if (is_array($connections) === false) {
            throw new ConfigException('Invalid Bindings Configuration');
        }
        $this->connections = $connections;
    }

    /**
     * Build the binding using  the specified model name
     *
     * @param mixed $modelName The name of the model or the model for which to buld
     * the binding
     *
     * @return Binding The binding
     */
    public function build($modelName)
    {
        $modelName = is_object($modelName) ? get_class($modelName) : $modelName;
        if (substr($modelName, 0, 1) !== '\\') {
            $modelName = '\\' . $modelName;
        }

        if (array_key_exists($modelName, $this->bindings)) {
            $binding = $this->bindings[$modelName];
        } else if (array_key_exists('default', $this->bindings)) {
            $binding = $this->bindings['default'];
        } else {
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
        if (array_key_exists($binding['connection'], $this->connections) === false) {
            throw new \Exception('Could not find ' . $binding['connection']);
        }
        $connection = $this->connections[$binding['connection']] + $binding;
        $bindingObj = new $bindingClass($connection);
        return $bindingObj;
    }
}
