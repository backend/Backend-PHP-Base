<?php
/**
 * File defining \Backend\Base\Tests\Formats\CliTest
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   BaseTests
 * @author    J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright 2011 - 2012 Jade IT (cc)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://backend-php.net
 */
namespace Backend\Base\Tests\Formats;
use \Backend\Base\Formats\Cli;
use Backend\Core\Request;
use Backend\Core\Response;

/**
 * Class to test the \Backend\Base\Formats\Cli class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class CliTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for the transform method.
     *
     * @return array
     */
    public function dataTransform()
    {
        $result = array();
        $result[] = array(false);
        $result[] = array(array());
        $result[] = array(new \stdClass);
        $result[] = array('Some Value');

        $exception = new \Exception;
        $exception->xdebug_message = 'test';
        $result[] = array($exception);

        $exception = new \Exception('Test', 700);
        $result[] = array($exception);

        $exception = new \Exception('Test', 99);
        $result[] = array($exception);

        $exception = new \Exception('Test', 404);
        $result[] = array($exception);

        return $result;
    }

    /**
     * Test the transform method.
     *
     * @return void
     * @dataProvider dataTransform
     */
    public function testTransform($body)
    {
        $request = $this->getMock('Backend\Interfaces\RequestInterface');

        $cli = new Cli($request);
        $result = $cli->transform($body);

        $this->assertInstanceOf('Backend\Interfaces\ResponseInterface', $result);
        $this->assertGreaterThanOrEqual(100, $result->getStatusCode());
        $this->assertLessThan(600, $result->getStatusCode());
    }

    /**
     * Test transforming responses
     *
     * @return void
     * @dataProvider dataTransform
     */
    public function testResponseTransform($body)
    {
        $response = new Response($body);

        $request = $this->getMock('Backend\Interfaces\RequestInterface');
        $cli = new Cli($request);

        $result = $cli->transform($response);
        $this->assertInstanceOf('Backend\Interfaces\ResponseInterface', $result);
        $this->assertGreaterThanOrEqual(100, $result->getStatusCode());
        $this->assertLessThan(600, $result->getStatusCode());

    }
}
