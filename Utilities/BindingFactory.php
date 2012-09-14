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
     * @param Backend\Interfaces\ConfigInterface $bindings    The bindings config.
     * @param Backend\Interfaces\ConfigInterface $connections The connections config.
     */
    public function __construct(ConfigInterface $bindings, ConfigInterface $connections)
    {
        // Setup Bindings
        $this->setBindings($bindings);

        // Setup Connections
        $this->setConnections($connections);
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

        if ($this->bindings->has($modelName) === false) {
            throw new ConfigException('No binding setup for ' . $modelName);
        }
        $binding = $this->getBinding($modelName);

        if (empty($binding['type'])) {
            throw new ConfigException('Missing Binding Type for ' . $modelName);
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

        if ($this->connections->has($binding['connection']) === false) {
            throw new ConfigException('Could not find Binding Connection: ' . $binding['connection']);
        }
        $connection = $this->getConnection($binding['connection']);

        $connection = $connection + $binding;
        $bindingObj = new $bindingClass($connection);
        return $bindingObj;
    }

    /**
     * Get the Bindings Configuration.
     *
     * @return Backend\Interfaces\ConfigInterface
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Set the Bindings Configuration.
     *
     * @param Backend\Interfaces\ConfigInterface
     *
     * @return Backend\Base\Utilities\BindingFactory
     */
    public function setBindings(ConfigInterface $bindings)
    {
        $this->bindings = $bindings;
        return $this;
    }

    /**
     * Get the specified Binding.
     *
     * @return array|null The Binding or null if it doesn't exist.
     */
    public function getBinding($name)
    {
        if ($this->bindings->has($name)) {
            return $this->bindings->get($name);
        }
        return null;
    }

    /**
     * Set the values of the specified Binding.
     *
     * @param array $binding The Binding described as an array.
     *
     * @return Backend\Base\Utilities\BindingFactory
     */
    public function setBinding($name, array $binding)
    {
        $this->bindings->set($name, $binding);
        return $this;
    }

    /**
     * Get the Connections Configuration.
     *
     * @return Backend\Interfaces\ConfigInterface
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Set the Connections Configuration.
     *
     * @param Backend\Interfaces\ConfigInterface
     *
     * @return Backend\Base\Utilities\BindingFactory
     */
    public function setConnections(ConfigInterface $connections)
    {
        $this->connections = $connections;
        return $this;
    }

    /**
     * Get the specified Connection.
     *
     * @return array|null The Connection or null if it doesn't exist.
     */
    public function getConnection($name)
    {
        if ($this->connections->has($name)) {
            return $this->connections->get($name);
        }
        return null;
    }

    /**
     * Set the values of the specified Connection.
     *
     * @param array $connection The Connection described as an array.
     *
     * @return Backend\Base\Utilities\BindingFactory
     */
    public function setConnection($name, array $connection)
    {
        $this->connections->set($name, $connection);
        return $this;
    }
}
