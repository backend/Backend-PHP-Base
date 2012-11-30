<?php
/**
 * File defining \Backend\Base\Tests\Formats\HtmlTest
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

use Backend\Base\Formats\Html;
use Backend\Core\Request;
use Backend\Core\Response;
use Backend\Base\Utilities\Renderable;

/**
 * Class to test the \Backend\Base\Formats\Html class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class HtmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the constructor.
     *
     * @return void
     */
    public function testConstructor()
    {
        $request = new Request('http://google.com/test/index.php/again/');
        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $render = $this->getMock('Backend\Interfaces\RenderInterface');
        $html = new Html($request, $config, $render);

        $this->assertSame($request, $html->getRequest());
        $this->assertSame($config, $html->getConfig());
        $this->assertSame($render, $html->getRender());
    }

    /**
     * Test the Config setter and getter.
     *
     * @return void
     */
    public function testConfigAccessors()
    {
        $request = $this->getMock('Backend\Interfaces\RequestInterface');
        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $render = $this->getMock('Backend\Interfaces\RenderInterface');
        $html = new Html($request, $config, $render);

        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $this->assertSame($html, $html->setConfig($config));
        $this->assertSame($config, $html->getConfig());
    }

    /**
     * Test the Render setter and getter.
     *
     * @return void
     */
    public function testRenderAccessors()
    {
        $request = $this->getMock('Backend\Interfaces\RequestInterface');
        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $render = $this->getMock('Backend\Interfaces\RenderInterface');
        $html = new Html($request, $config, $render);

        $render = $this->getMock('Backend\Interfaces\RenderInterface');
        $html->setRender($render);
        $this->assertSame($render, $html->getRender());
    }

    /**
     * Test if the correct calls are made to the response.
     *
     * @return void
     */
    public function testResponseCalls()
    {
        $request = $this->getMock('Backend\Interfaces\RequestInterface');
        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $render   = $this->getMock('Backend\Interfaces\RenderInterface');

        $response = $this->getMock('Backend\Interfaces\ResponseInterface');
        $response
            ->expects($this->once())
            ->method('setHeader')
            ->with('Content-Type', 'text/html; charset=utf-8');
        $response
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue('<html><body>Some Body</body></html>'));
        $response
            ->expects($this->once())
            ->method('setBody')
            ->with($this->isType('string'));

        $html = new Html($request, $config, $render);
        $result = $html->transform($response);
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

        $render   = $this->getMock('Backend\Interfaces\RenderInterface');

        $renderable = new Renderable($render, 'test', array());
        $result[] = array($renderable);

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
        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $render   = $this->getMock('Backend\Interfaces\RenderInterface');

        $html = new Html($request, $config, $render);
        $result = $html->transform($body);

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
        $config = $this->getMock('Backend\Interfaces\ConfigInterface');
        $render   = $this->getMock('Backend\Interfaces\RenderInterface');

        $html = new Html($request, $config, $render);

        $result = $html->transform($response);
        $this->assertInstanceOf('Backend\Interfaces\ResponseInterface', $result);
        $this->assertGreaterThanOrEqual(100, $result->getStatusCode());
        $this->assertLessThan(600, $result->getStatusCode());

    }
}
