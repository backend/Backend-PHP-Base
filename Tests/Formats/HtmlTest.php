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
use \Backend\Base\Formats\Html;
use Backend\Core\Request;

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
        $html->setConfig($config);
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

        $renderable = $this->getMock(
            'Backend\Base\Utilities\Renderable', null, array(),
            'Backend\Base\Utilities\Renderable', false
        );
        $result[] = array($renderable);
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
        $render
            ->expects($this->once())
            ->method('file');

        $response = $this->getMock('Backend\Interfaces\ResponseInterface');
        $response
            ->expects($this->once())
            ->method('addHeader')
            ->with('Content-Type', 'text/html; charset=utf-8');
        $response
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($body));

        $html = new Html($request, $config, $render);
        $html->transform($response);
    }

    /**
     * Test skipping the transform if we can't render
     *
     * @return void
     */
    public function testSkipTransform()
    {

    }
}
