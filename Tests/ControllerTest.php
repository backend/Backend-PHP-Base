<?php
/**
 * File defining Backend\Base\ControllerTest
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   CoreTests
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
 * @package  CoreTests
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
}
