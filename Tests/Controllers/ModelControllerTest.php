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
    protected $requestContext;
    protected $bindingFactory;
    /**
     * Set up the test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->requestContext = $this->getMock('\Backend\Interfaces\RequestContextInterface');
        $this->requestContext
            ->expects($this->any())
            ->method('getLink')
            ->will($this->returnValue('http://backend-php.net'));

        $this->bindingFactory = $this->getMockForAbstractClass('\Backend\Interfaces\BindingFactoryInterface');

        $valueMap = array(
            array('request_context', null, $this->requestContext),
            array('binding_factory', null, $this->bindingFactory),
        );

        $this->container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $this->container
            ->expects($this->any())
            ->method('getParameter')
            ->with('response.class')
            ->will($this->returnValue('\Backend\Core\Response'));
        $this->container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($valueMap));

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
        $this->request = null;
        $this->controller = null;
        $this->requestContext = null;
        $this->bindingFactory = null;
    }

    /**
     * Test the create action.
     *
     * @return void
     */
    public function testCreate()
    {
        $data = array('one' => 'value');
        $this->request
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($data));

        $model = $this->getMockForAbstractClass('\Backend\Interfaces\ModelInterface');
        $binding = $this->getMockForAbstractClass('\Backend\Interfaces\BindingInterface');
        $binding
            ->expects($this->once())
            ->method('create')
            ->with($data)
            ->will($this->returnValue($model));

        $this->bindingFactory
            ->expects($this->once())
            ->method('build')
            ->with('\Backend\Base\Models\Model')
            ->will($this->returnValue($binding));

        $result = $this->controller->createAction(1);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals(201, $result->getStatusCode());
    }

    /**
     * Test the updateHtml method.
     *
     * @return void
     */
    public function testResponseCreateHtml()
    {
        $model = $this->getMock('\Backend\Base\Model');

        $this->request
            ->expects($this->once())
            ->method('getHeader')
            ->with('referer')
            ->will($this->returnValue('http://backend-php.net/value'));

        $result = $this->getMock('\Backend\Interfaces\ResponseInterface', array(), array('', 204));
        $result
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(201));
        $result
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($model));

        $result
            ->expects($this->once())
            ->method('setStatusCode')
            ->with(302)
            ->will($this->returnSelf());
        $result
            ->expects($this->once())
            ->method('setHeader')
            ->with('Location', 'http://backend-php.net/value')
            ->will($this->returnSelf());

        $actual = $this->controller->createHtml($result);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
    }

    /**
     * Test the updateHtml method.
     *
     * @return void
     */
    public function testResponseModelIdCreateHtml()
    {
        $model = $this->getMock('\Backend\Base\Model', array('getId'));
        $model
            ->expects($this->once())
            ->method('getId')
            ->will($this->returnValue(321));

        $this->request
            ->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('http://backend-php.net/value'));

        $result = $this->getMock('\Backend\Interfaces\ResponseInterface', array(), array('', 204));
        $result
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(201));
        $result
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($model));

        $result
            ->expects($this->once())
            ->method('setStatusCode')
            ->with(302)
            ->will($this->returnSelf());
        $result
            ->expects($this->once())
            ->method('setHeader')
            ->with('Location', 'http://backend-php.net/value/321')
            ->will($this->returnSelf());

        $actual = $this->controller->createHtml($result);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
    }

    /**
     * Test the updateHtml method.
     *
     * @return void
     */
    public function testNoResponseCreateHtml()
    {
        $this->request
            ->expects($this->once())
            ->method('getHeader')
            ->with('referer')
            ->will($this->returnValue('http://backend-php.net/value'));

        $actual = $this->controller->createHtml(false);

        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
        $this->assertContains('Location: http://backend-php.net/value', $actual->getHeaders());
    }

    /**
     * Test the read action.
     *
     * @return void
     */
    public function testRead()
    {
        $binding = $this->getMockForAbstractClass('\Backend\Interfaces\BindingInterface');
        $binding
            ->expects($this->once())
            ->method('read')
            ->with(1)
            ->will($this->returnValue(true));
        $bindingFactory = $this->getMockForAbstractClass('\Backend\Interfaces\BindingFactoryInterface');
        $this->bindingFactory
            ->expects($this->once())
            ->method('build')
            ->with('\Backend\Base\Models\Model')
            ->will($this->returnValue($binding));
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with('binding_factory')
            ->will($this->returnValue($bindingFactory));
        $this->assertTrue($this->controller->readAction(1));
    }

    /**
     * Test trying to read an non existant model.
     *
     * @return void
     */
    public function testRead404()
    {
        $binding = $this->getMockForAbstractClass('\Backend\Interfaces\BindingInterface');
        $binding
            ->expects($this->once())
            ->method('read')
            ->with(1)
            ->will($this->returnValue(null));
        $this->bindingFactory
            ->expects($this->once())
            ->method('build')
            ->with('\Backend\Base\Models\Model')
            ->will($this->returnValue($binding));

        $result = $this->controller->readAction(1);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals(404, $result->getStatusCode());
    }

    /**
     * Test the update action.
     *
     * @return void
     */
    public function testUpdate()
    {
        $data = array('one' => 'value');
        $this->request
            ->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($data));

        $model = $this->getMockForAbstractClass('\Backend\Interfaces\ModelInterface');
        $model
            ->expects($this->once())
            ->method('populate')
            ->with($data);
        $binding = $this->getMockForAbstractClass('\Backend\Interfaces\BindingInterface');
        $binding
            ->expects($this->once())
            ->method('read')
            ->with(1)
            ->will($this->returnValue($model));
        $binding
            ->expects($this->once())
            ->method('update')
            ->with($model);

        $this->bindingFactory
            ->expects($this->exactly(2))
            ->method('build')
            ->with('\Backend\Base\Models\Model')
            ->will($this->returnValue($binding));

        $result = $this->controller->updateAction(1);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals(204, $result->getStatusCode());
    }

    /**
     * Test trying to update a record that doesn't exist.
     *
     * @return void
     */
    public function testUpdate404()
    {
        $binding = $this->getMockForAbstractClass('\Backend\Interfaces\BindingInterface');
        $binding
            ->expects($this->once())
            ->method('read')
            ->with(1)
            ->will($this->returnValue(null));
        $this->bindingFactory
            ->expects($this->once())
            ->method('build')
            ->with('\Backend\Base\Models\Model')
            ->will($this->returnValue($binding));

        $result = $this->controller->updateAction(1);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals(404, $result->getStatusCode());
    }

    /**
     * Test the updateHtml method.
     *
     * @return void
     */
    public function testResponseUpdateHtml()
    {
        $this->request
            ->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('http://backend-php.net/value/1'));
        $result = $this->getMock('\Backend\Interfaces\ResponseInterface', array(), array('', 204));
        $result
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(204));
        $result
            ->expects($this->once())
            ->method('setStatusCode')
            ->with(302)
            ->will($this->returnSelf());
        $result
            ->expects($this->once())
            ->method('setHeader')
            ->with('Location', 'http://backend-php.net/value/1')
            ->will($this->returnSelf());

        $actual = $this->controller->updateHtml($result);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
    }

    /**
     * Test the updateHtml method.
     *
     * @return void
     */
    public function testNoResponseUpdateHtml()
    {
        $this->request
            ->expects($this->once())
            ->method('getUrl')
            ->will($this->returnValue('http://backend-php.net/value/1'));

        $actual = $this->controller->updateHtml(false);

        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
        $this->assertEquals(302, $actual->getStatusCode());
        $this->assertContains('Location: http://backend-php.net/value/1', $actual->getHeaders());
    }

    /**
     * Test the delete action.
     *
     * @return void
     */
    public function testDelete()
    {
        $model = $this->getMockForAbstractClass('\Backend\Interfaces\ModelInterface');
        $binding = $this->getMockForAbstractClass('\Backend\Interfaces\BindingInterface');
        $binding
            ->expects($this->once())
            ->method('read')
            ->with(1)
            ->will($this->returnValue($model));
        $binding
            ->expects($this->once())
            ->method('delete')
            ->with($model);

        $this->bindingFactory
            ->expects($this->exactly(2))
            ->method('build')
            ->with('\Backend\Base\Models\Model')
            ->will($this->returnValue($binding));

        $result = $this->controller->deleteAction(1);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals(204, $result->getStatusCode());
    }

    /**
     * Test trying to delete a record that doesn't exist.
     *
     * @return void
     */
    public function testDelete404()
    {
        $binding = $this->getMockForAbstractClass('\Backend\Interfaces\BindingInterface');
        $binding
            ->expects($this->once())
            ->method('read')
            ->with(1)
            ->will($this->returnValue(null));
        $this->bindingFactory
            ->expects($this->once())
            ->method('build')
            ->with('\Backend\Base\Models\Model')
            ->will($this->returnValue($binding));

        $result = $this->controller->deleteAction(1);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals(404, $result->getStatusCode());
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
        $result = $this->getMock('\Backend\Interfaces\ResponseInterface', array(), array('', 204));
        $result
            ->expects($this->once())
            ->method('getStatusCode')
            ->will($this->returnValue(204));
        $result
            ->expects($this->once())
            ->method('setStatusCode')
            ->with(302)
            ->will($this->returnSelf());
        $result
            ->expects($this->once())
            ->method('setHeader')
            ->with('Location', '/test/path')
            ->will($this->returnSelf());

        $actual = $this->controller->deleteHtml($result);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
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

        $actual = $this->controller->deleteHtml(false);

        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $actual);
        $this->assertEquals(302, $actual->getStatusCode());
        $this->assertContains('Location: http://backend-php.net/test/path', $actual->getHeaders());
    }
}
