<?php
/**
 * File defining \Base\Views\Json
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Views
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Views;
use \Backend\Core\Response;
use \Backend\Core\Decorators\JsonDecorator;
use \Backend\Core\Interfaces\DecorableInterface;
/**
 * Output a request in JavaScript Object Notation
 *
 * @category   Backend
 * @package    Base
 * @subpackage Views
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Json extends \Backend\Core\View
{
    /**
     * @var array Handle JSON requests
     */
    public static $handledFormats = array('json', 'text/json', 'application/json', 'text/javascript');

    /**
     * Transform the result into a Response Object containing the JSON encoded result
     *
     * @param mixed $result The result to transform
     *
     * @return Response The result transformed into a JSON encoded Response
     */
    public function transform($result)
    {
        if ($result instanceof Response) {
            $response = $result;
            $body     = $response->getBody();
        } else {
            $response = new Response();
            $body     = $result;
        }
        $response->addHeader('X-Backend-View', get_class($this));

        if ($body instanceof DecorableInterface) {
            $body = new JsonDecorator($body);
            $body = $body->toJson();
        } else if (is_callable(array($body, 'toJson'))) {
            $body = $body->toJson();
        } else {
            $body = json_encode($body);
        }
        $response->setBody($body);
        return $response;
    }
}
