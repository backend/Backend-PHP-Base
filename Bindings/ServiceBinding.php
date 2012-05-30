<?php
/**
 * File defining \Backend\Base\Bindings\ServiceBinding
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
 * Service Connection Binding
 *
 * @category   Backend
 * @package    Base
 * @subpackage Bindings
 * @author     J Jurgens du Toit <jrgns@jrgns.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
abstract class ServiceBinding extends Binding
{
    protected $url = null;

    protected $chandle = null;

    /**
     * The constructor for the object.
     *
     * @param array $settings The settings for the Service Connection
     */
    public function __construct(array $settings)
    {
        $connection = empty($settings['connection']) ? 'default' : $settings['connection'];

        $config = ServiceLocator::get('backend.Config');
        $settings = $config->get('remote_service', $connection);
        if (empty($settings['url'])) {
            throw new \Exception('No Service settings for ' . $connection);
        }
        $this->url = $settings['url'];

        $this->chandle = curl_init($this->url);

        if (isset($settings['username']) && isset($settings['password'])) {
            curl_setopt($this->chandle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($this->chandle, CURLOPT_USERPWD, $settings['username'] . ':' . $settings['password']);
        }
    }
}
