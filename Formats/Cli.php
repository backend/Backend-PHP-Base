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
use Backend\Core\Response;
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
            $code = $result->getStatusCode();
            $body = $result->getBody();
        } else {
            $code = 200;
            $body = $result;
            $result = new Response(null, 200);
        }
        $content = 'Result:' . PHP_EOL;
        switch (true) {
            case $result instanceof \Exception:
                $content .= 'Exception: ' . $body->getMessage() . ' (' . $body->getCode() . ')' . PHP_EOL;
                $content .= 'File: ' . $body->getFile() . PHP_EOL;
                $content .= 'Line: ' . $body->getLine() . PHP_EOL;

                $code = $body->getCode();
                $body = $content;
                break;
            case is_object($result) && method_exists($result, '__toString') === false:
                $content .= 'Object: ' . get_class($body);
                break;
            case is_array($result):
                $content .= var_export($body, true);
                break;
            default:
                $content .= (string) $result;
                break;
        }
        if ($code > 600 || $code < 100) {
            $code = 500;
        }
        $body .= PHP_EOL;

        return $result
            ->setBody($body)
            ->setStatusCode($code);
    }
}
