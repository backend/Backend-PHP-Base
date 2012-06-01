<?php
/**
 * File defining \Backend\Base\Bindings\DatabaseBinding
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Bindings;
use \Backend\Core\Utilities\ServiceLocator;
/**
 * Database Connection Binding
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
abstract class DatabaseBinding extends Binding
{
    /**
     * The constructor for the object.
     *
     * @param array $settings The settings for the Database Connection
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);

        $config = ServiceLocator::get('backend.Config');
        $connection = $settings['connection'];
        $settings   = $config->get('database', $connection);
        $this->init($settings['connection']);

    }

    /**
     * Initialize the connection
     *
     * @param array $connection The connection information for the binding
     *
     * @return Object The current object 
     */
    protected abstract function init(array $connection);
}
