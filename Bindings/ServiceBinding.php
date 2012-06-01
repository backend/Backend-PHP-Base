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

    protected $base = null;

    /**
     * The constructor for the object.
     *
     * @param array $settings The settings for the Service Connection
     */
    public function __construct(array $settings)
    {
        parent::__construct($settings);
        $connection = $settings['connection'];

        $config = ServiceLocator::get('backend.Config');
        $settings = $config->get('remote_service', $connection);
        if (empty($settings['url'])) {
            throw new \Exception('No Service settings for ' . $connection);
        }
        $this->url = $settings['url'];

        $this->chandle = curl_init();

        if (isset($settings['username']) && isset($settings['password'])) {
            curl_setopt($this->chandle, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
            curl_setopt($this->chandle, CURLOPT_USERPWD, $settings['username'] . ':' . $settings['password']);
        }

        curl_setopt($this->chandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->chandle, CURLOPT_HEADER, true);
    }

    public function get($path = null, array $data = array())
    {
        curl_setopt($this->chandle, CURLOPT_HTTPGET);
        $path .= '?' . http_build_query($data);
        return $this->execute($path);
    }

    public function post($path = null, array $data = array())
    {
        curl_setopt($this->chandle, CURLOPT_POST);
        curl_setopt($this->chandle, CURLOPT_POSTFIELDS, $data);
        return $this->execute($path);
    }

    public function execute($path = null)
    {
        if ($path && substr($path, 0, 1) != '/' && (substr($this->url, -1) != '/')) {
            $path = '/' . $path;
        }
        curl_setopt($this->chandle, CURLOPT_URL, $this->url . $path);
        $result = curl_exec($this->chandle);
        if ($result === false) {
            throw new \Exception('Curl Issue: ' . curl_error($this->chandle), $curl_errno($this->chandle));
        }
        $code  = curl_getinfo($this->chandle, CURLINFO_HTTP_CODE);
        if ($code === 200) {
            return explode("\r\n\r\n", $result) + array(1 => '');
        }
        return false;
    }
}
