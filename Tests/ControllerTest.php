<?php
/**
 * File defining Backend\Base\ControllerTest
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
namespace Backend\Base\Tests;
use \Backend\Base\Controller;
/**
 * Class to test the \Backend\Base\Controller class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test adding a flash message.
     *
     * @return void
     */
    public function testAddFlash()
    {
        $flash = $this->getMockForAbstractClass(
            '\Backend\Interfaces\FlashInterface'
        );
        $flash
            ->expects($this->once())
            ->method('set')
            ->with('this', 'message');
        $container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $container
            ->expects($this->any())
            ->method('has')
            ->with('flash')
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('get')
            ->with('flash')
            ->will($this->returnValue($flash));
        $controller = new Controller();
        $controller->setContainer($container);
        $controller->flash('this', 'message');
    }

    /**
     * Test adding a flash message without a flash service.
     *
     * @return void
     */
    public function testLogFlashWhenNoService()
    {
        $logger = $this->getMockForAbstractClass(
            '\Backend\Interfaces\LoggerInterface'
        );
        $logger
            ->expects($this->once())
            ->method('debug');
        $container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $container
            ->expects($this->any())
            ->method('has')
            ->will($this->onConsecutiveCalls(false, true, true));
        $container
            ->expects($this->once())
            ->method('get')
            ->with('logger')
            ->will($this->returnValue($logger));
        $controller = new Controller();
        $controller->setContainer($container);
        $controller->flash('this', 'message');
    }

    /**
     * Test getting a service through the magic __get method.
     *
     * @return void
     */
    public function testMagicGetService()
    {
        $container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $container
            ->expects($this->once())
            ->method('has')
            ->with('some_service')
            ->will($this->returnValue(true));
        $container
            ->expects($this->once())
            ->method('get')
            ->with('some_service')
            ->will($this->returnValue(true));
        $controller = new Controller();
        $controller->setContainer($container);
        $this->assertTrue($controller->some_service);
    }

    /**
     * Test throwing an exception when an undefined service is requested.
     *
     * @return void
     * @expectedException \ErrorException
     * @expectedExceptionMessage Undefined property
     */
    public function testUndefinedService()
    {
        $container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $container
            ->expects($this->once())
            ->method('has')
            ->with('some_service')
            ->will($this->returnValue(false));
        $controller = new Controller();
        $controller->setContainer($container);
        $controller->some_service;
    }

    /**
     * Test adding the session when rendering.
     *
     * @return void
     */
    public function testAddSessionAndFlashOnRender()
    {
        $container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $hasMap = array(
            array('session', true),
            array('renderer', true),
            array('user_session', false),
            array('flash', true),
        );
        $container
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($hasMap));

        $renderer = $this->getMockForAbstractClass(
            '\Backend\Interfaces\RenderInterface'
        );
        $session = $this->getMockForAbstractClass(
            '\Backend\Interfaces\SessionInterface'
        );
        $flash = $this->getMockForAbstractClass(
            '\Backend\Interfaces\FlashInterface'
        );
        $getMap = array(
            array('session', null, $session),
            array('renderer', null, $renderer),
            array('flash', null, $flash),
        );
        $container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($getMap));

        $controller = new Controller();
        $controller->setContainer($container);
        $result = $controller->render('template', array());
        $this->assertInstanceOf('\Backend\Base\Utilities\Renderable', $result);
        $values = $result->getValues();
        $this->assertInternalType('array', $values);
        $this->assertArrayHasKey('session', $values);
        $this->assertSame($session, $values['session']);
        $this->assertArrayHasKey('flash', $values);
        $this->assertSame($flash, $values['flash']);
    }

    /**
     * Test not overwriting existing session and flash.
     *
     * @return void
     */
    public function testDontOverwriteExistingSessionAndFlash()
    {
        $container = $this->getMockForAbstractClass(
            '\Backend\Interfaces\DependencyInjectionContainerInterface'
        );
        $hasMap = array(
            array('session', true),
            array('renderer', true),
            array('user_session', false),
            array('flash', false),
        );
        $container
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValueMap($hasMap));

        $renderer = $this->getMockForAbstractClass(
            '\Backend\Interfaces\RenderInterface'
        );
        $getMap = array(
            array('renderer', null, $renderer),
        );
        $container
            ->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap($getMap));

        $values = array(
            'session' => array(),
            'flash'   => array(),
        );
        $controller = new Controller();
        $controller->setContainer($container);
        $result = $controller->render('template', $values);

        $this->assertSame($values, $result->getValues());
    }
}
