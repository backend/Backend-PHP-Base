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
     * The class constructor.
     *
     * @param Backend\Interfaces\ConfigInterface|array $config The bindings config as
     * a Config object or an array
     */
    public function __construct($config)
    {
        if ($config instanceof ConfigInterface) {
            $config = $config->get();
        } else if (is_object($config)) {
            $config = (array)$config;
        } else if (is_array($config) === false) {
            throw new ConfigException('Invalid Bindings Configuration');
        }
        $this->bindings = $config;
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

        if (array_key_exists($modelName, $this->bindings) === false) {
            throw new \Exception('No binding setup for ' . $modelName);
        }
        $binding = $this->bindings[$modelName];
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
        try {
            $bindingObj = new $bindingClass($binding);
        } catch (\Exception $e) {
            var_dump($e); die;
        }
        return $bindingObj;
    }
}
