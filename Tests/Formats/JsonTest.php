<?php
/**
 * File defining \Backend\Base\Tests\Formats\JsonTest
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

use Backend\Base\Formats\Json;
use Backend\Core\Request;
use Backend\Core\Response;

/**
 * Class to test the \Backend\Base\Formats\Json class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class JsonTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if the correct calls are made to the response.
     *
     * @return void
     */
    public function testResponseHeaderAndBody()
    {
        $request = $this->getMock('Backend\Interfaces\RequestInterface');
        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $render   = $this->getMock('Backend\Interfaces\RenderInterface');

        $json = new Json($request, $config, $render);
        $result = $json->transform('string');
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals('application/json', $result->getHeader('Content-Type'));
        $this->assertEquals('"string"', $result->getBody());
    }

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

        $result[] = array(new \Backend\Base\Model);

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

        $json = new Json($request);
        $result = $json->transform($body);

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
        $json = new Json($request);

        $result = $json->transform($response);
        $this->assertInstanceOf('Backend\Interfaces\ResponseInterface', $result);
        $this->assertGreaterThanOrEqual(100, $result->getStatusCode());
        $this->assertLessThan(600, $result->getStatusCode());

    }

    /**
     * Test json encoding error
     *
     * @return void
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Json Encoding Error
     */
    public function testJsonEncodingError()
    {
        $request = $this->getMock('Backend\Interfaces\RequestInterface');
        $json = new Json($request);

        $json->transform("\xB1\x31");
    }
}
