<?php
/**
 * File defining \Base\Views\Cli
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
/**
 * Output a request on the Command Line.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Views
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Cli extends \Backend\Core\View
{
    /**
     * @var array Handle CLI requests
     */
    public static $handledFormats = array('cli');

    /**
     * Transform the result into a Response Object containing the result for output
     * on a Command Line Interface
     *
     * @param mixed $result The result to transform
     *
     * @return Response The result transformed into a CLI appropriate Response
     */
    function transform($result)
    {
        $result = 'Result:' . PHP_EOL;
        $result .= var_export($result, $true);
        $result .= PHP_EOL;
        $response = new Response($result, 200);
        $response->addHeader('X-Backend-View', get_class($this));
        return $response;
    }
}
