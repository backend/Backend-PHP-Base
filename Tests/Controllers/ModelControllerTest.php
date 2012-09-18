<?php
/**
 * File defining Backend\Base\Controllers\ModelControllerTest
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
namespace Backend\Base\Tests\Controllers;
use Backend\Base\Controllers\ModelController;
use Backend\Core\Response;
require_once __DIR__ . '/../auxiliary/TestModel.php';
/**
 * Class to test the \Backend\Base\Controllers\ModelController class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class ModelControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $container;
    protected $request;
    protected $controller;
    /**
     * Set up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $this->request = $this->getMockForAbstractClass('\Backend\Interfaces\RequestInterface');
        $this->controller = new ModelController($this->container, $this->request);
    }

    /**
     * Tear down the test.
     *
     * @return void
     */
    protected function tearDown()
    {
        $this->container = null;
        $this->controller = null;
    }

    /**
     * Test the deleteHtml method.
     *
     * @return void
     */
    public function testResponseDeleteHtml()
    {
        $this->request
            ->expects($this->once())
            ->method('getHeader')
            ->with('referer')
            ->will($this->returnValue('/test/path'));
        $this->container
            ->expects($this->once())
            ->method('getParameter')
            ->with('response.class')
            ->will($this->returnValue('\Backend\Core\Response'));
        $result = $this->getMock('\Backend\Interfaces\ResponseInterface', array(), array('', 204));
        $result
            ->expects($this->once())
            ->method('setHeader')
            ->with('Location', '/test/path')
            ->will($this->returnSelf());
        $result
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(204));

        $actual = $this->controller->deleteHtml($result);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
        $this->assertEquals(302, $actual->getStatusCode());
        $this->assertContains('Location: /test/path', $actual->getHeaders());
    }

    /**
     * Test the deleteHtml method.
     *
     * @return void
     */
    public function testNoResponseDeleteHtml()
    {
        $this->request
            ->expects($this->once())
            ->method('getHeader')
            ->with('referer')
            ->will($this->returnValue('/test/path'));
        $this->container
            ->expects($this->once())
            ->method('getParameter')
            ->with('response.class')
            ->will($this->returnValue('\Backend\Core\Response'));

        $actual = $this->controller->deleteHtml(false);

        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
        $this->assertEquals(302, $actual->getStatusCode());
        $this->assertContains('Location: /test/path', $actual->getHeaders());
    }

    /**
     * data for testGetModelName
     *
     * @return array
     */
    public function dataGetModelName()
    {
        $this->setUp();
        $result = array();
        $result[] = array($this->controller, '\Backend\Base\Models\Model');
        $result[] = array('\Backend\Base\Controllers\ValuesController', '\Backend\Base\Models\Value');
        return $result;
    }

    /**
     * Test the getModelName method.
     *
     * @return void
     * @dataProvider dataGetModelName
     */
    public function testGetModelName($argument, $expected)
    {
        $actual = ModelController::getModelName($argument);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test the getBinding method.
     *
     * @return void
     */
    public function testGetBinding()
    {
        $bindingFactory = $this->getMockForAbstractClass(
            '\Backend\Interfaces\BindingFactoryInterface'
        );
        $bindingFactory
            ->expects($this->once())
            ->method('build')
            ->with('\TestModel');
        $this->container
            ->expects($this->any())
            ->method('get')
            ->with('binding_factory')
            ->will($this->returnValue($bindingFactory));
        $this->controller->getBinding('\TestModel');

    }
}
