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
use \Backend\Core\Application;
/**
 * Database Connection Binding
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 * @todo This is a rudimentary implementation of the PDOBinding. It can be improved a lot.
 */
abstract class DatabaseBinding extends Binding
{
    /**
     * @var string The name of the table this binding operates on
     */
    protected $table;

    public function __construct($connection, $table)
    {
        $config = Application::getTool('Config');
        $settings = $config->get('database', $connection);
        $this->init($settings['connection']);

        $this->table = $table;
    }

    protected abstract function init(array $connection);
}
