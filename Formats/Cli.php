<?php
/**
 * File defining \Base\Formats\Cli
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
/**
 * Output a request on the Command Line.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Formats
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class Cli extends \Backend\Core\Utilities\Formatter
{
    /**
     * @var array Handle CLI requests
     */
    public static $handledFormats = array('cli');

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
        if ($result instanceof Response) {
            $code   = $result->getStatusCode();
            $result = $result->getBody();
        } else {
            $code = 500;
        }
        $body = 'Result:' . PHP_EOL;
        switch (true) {
            case $result instanceof \Exception:
                $body .= 'Exception: ' . $result->getMessage() . ' (' . $result->getCode() . ')' . PHP_EOL;
                $body .= 'File: ' . $result->getFile() . PHP_EOL;
                $body .= 'Line: ' . $result->getLine() . PHP_EOL;
                break;
            default:
                $body .= (string) $result;
                break;
        }
        $body .= PHP_EOL;
        return new Response($body, $code);
    }
}
