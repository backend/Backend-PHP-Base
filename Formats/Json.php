<?php
/**
 * File defining \Base\Formats\Json
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Formats
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Formats;

use Backend\Core\Response;

/**
 * Output a request in JavaScript Object Notation.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Formats
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Json extends \Backend\Core\Utilities\Formatter
{
    /**
     * @var array Handle JSON requests
     */
    public static $handledFormats = array('json', 'text/json', 'application/json', 'text/javascript');

    /**
     * Transform the result into the Format.
     *
     * @param mixed $result The result to transform
     *
     * @return \Backend\Core\Response The response to transform
     */
    public function transform($result)
    {
        $response = parent::transform($result);
        $response->setHeader('Content-Type', 'application/json');

        $body = $response->getBody();
        if (is_callable(array($body, 'toJson'))) {
            $body = $body->toJson();
        } else {
            $body = json_encode($body);
            if ($error = json_last_error()) {
                throw new \RuntimeException('Json Encoding Error: ' . $error);
            }
        }
        $response->setBody($body);

        return $response;
    }
}
