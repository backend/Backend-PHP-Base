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
use \Backend\Core\Response;
use \Backend\Core\Interfaces\DecorableInterface;
use \Backend\Core\Decorators\JsonDecorator;
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
     * @param mixed    $result    The result to transform
     * @param callable $callback  The callback that was executed
     * @param array    $arguments The arguments that were passed
     *
     * @return \Backend\Core\Response The response to transform
     */
    public function transform($result)
    {
        $response = parent::transform($result, $callback, $arguments);
        $response->addHeader('Content-Type', 'application/json');

        $body = $response->getBody();
        if (is_callable(array($body, 'toJson'))) {
            $body = $body->toJson();
        } else if ($body instanceof DecorableInterface) {
            $body = new JsonDecorator($body);
            $body = $body->toJson();
        } else if (is_callable(array($body, 'getProperties'))) {
            $body = json_encode($body->getProperties());
            if ($error = json_last_error()) {
                throw new \Exception('Json Encoding Error: ' . $error);
            }
        } else {
            $body = json_encode($body);
            if ($error = json_last_error()) {
                throw new \Exception('Json Encoding Error: ' . $error);
            }
        }
        $response->setBody($body);
        return $response;
    }
}
