<?php
/**
 * File defining Backend\Base\Tests\Listener\BaseListenerTest
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
namespace Backend\Base\Tests\Listener;

use Backend\Base\Listener\BaseListener;
use Backend\Core\Utilities\Config;
use Backend\Core\Utilities\DependencyInjectionContainer;

/**
 * Class to test the \Backend\Base\Listener\BaseListener class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class BaseListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $container = null;

    /**
     * Set up the test
     *
     * Set the debugging level to 1, set a Request
     *
     * @return void
     */
    public function setUp()
    {
        $parser = new \Symfony\Component\Yaml\Parser;
        $config = new Config($parser, __DIR__ . '/../auxiliary/configs/application.testing.yml');
        $this->container = new DependencyInjectionContainer($config);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::__construct
     * @covers Backend\Base\Listener\BaseListener::getContainer
     */
    public function testConstructor()
    {
        $listener = new BaseListener($this->container);
        $this->assertSame($this->container, $listener->getContainer());
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreInitEvent
     * @return void
     */
    public function testSessionInitOnInitEvent()
    {
        $session = $this->getMockForAbstractClass('\Backend\Interfaces\SessionInterface');

        $this->container->set('session', $session);

        $event = $this->getMock('Symfony\Component\EventDispatcher\Event');
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $listener = new BaseListener($this->container);
        $listener->coreInitEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreRequestEvent
     * @return void
     */
    public function testLogRequestOnRequestEvent()
    {
        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');

        $logger
            ->expects($this->once())
            ->method('info');

        $this->container->set('logger', $logger);

        $request = $this->getMockForAbstractClass('\Backend\Interfaces\RequestInterface');

        $event = $this->getMock(
            'Backend\Core\Event\RequestEvent',
            array('getRequest'),
            array($request)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $event
            ->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue($request));

        $listener = new BaseListener($this->container);
        $listener->coreRequestEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreCallbackEvent
     * @return void
     */
    public function testAuthenticatorOnCallbackEvent()
    {
        $callback = $this->getMockForAbstractClass('\Backend\Interfaces\CallbackInterface');

        $authenticator = $this->getMockForAbstractClass('\Backend\Interfaces\AuthenticatorInterface');
        $authenticator
            ->expects($this->once())
            ->method('check')
            ->with($callback);

        $this->container->set('authenticator', $authenticator);

        $event = $this->getMock(
            'Backend\Core\Event\CallbackEvent',
            null,
            array($callback, $this->container)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $listener = new BaseListener($this->container);
        $listener->coreCallbackEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreCallbackEvent
     * @return void
     */
    public function testLoggerOnCallbackEvent()
    {
        $callback = $this->getMockForAbstractClass('\Backend\Interfaces\CallbackInterface');

        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('info');

        $this->container->set('logger', $logger);

        $exception = new \Exception('Message', 500);

        $event = $this->getMock(
            'Backend\Core\Event\CallbackEvent',
            null,
            array($callback)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $listener = new BaseListener($this->container);
        $listener->coreCallbackEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreResultEvent
     * @covers Backend\Base\Listener\BaseListener::callbackFormat
     * @return void
     */
    public function testResultEventWithEmptyMethod()
    {
        $result = new \stdClass;

        $response = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');

        $formatter = $this->getMockForAbstractClass('\Backend\Interfaces\FormatterInterface');
        $formatter
            ->expects($this->once())
            ->method('transform')
            ->with($result)
            ->will($this->returnValue($response));

        $this->container->set('formatter', $formatter);

        $event = $this->getMock(
            'Backend\Core\Event\ResultEvent',
            null,
            array($result)
        );

        $callback = $this->getMockForAbstractClass(
            '\Backend\Interfaces\CallbackInterface'
        );
        $this->container->set('callback', $callback);

        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $listener = new BaseListener($this->container);
        $listener->coreResultEvent($event);
    }

    /**
     * @return void
     * @covers Backend\Base\Listener\BaseListener::coreResultEvent
     * @expectedException Backend\Core\Exception
     * @expectedExceptionMessage Unsupported format requested
     * @expectedExceptionCode 415
     */
    public function testUnsupportedFormatResultEvent()
    {
        $event = $this->getMock(
            'Backend\Core\Event\ResultEvent',
            null,
            array(true)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $request = $this->getMockForAbstractClass(
            '\Backend\Interfaces\RequestInterface'
        );
        $this->container->set('request', $request);

        $listener = new BaseListener($this->container);
        $listener->coreResultEvent($event);
    }

    /**
     * @return void
     * @covers Backend\Base\Listener\BaseListener::coreResultEvent
     */
    public function testDefaultFormatterResultEvent()
    {
        $result = new \stdClass;

        $event = $this->getMock(
            'Backend\Core\Event\ResultEvent',
            null,
            array($result)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $response = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');

        $formatter = $this->getMockForAbstractClass('\Backend\Interfaces\FormatterInterface');
        $formatter
            ->expects($this->once())
            ->method('transform')
            ->with($result)
            ->will($this->returnValue($response));

        $this->container->set('backend.base.formats.html', $formatter);

        $callback = $this->getMockForAbstractClass(
            '\Backend\Interfaces\CallbackInterface'
        );
        $this->container->set('callback', $callback);

        $listener = new BaseListener($this->container);
        $listener->coreResultEvent($event);

        $this->assertSame($response, $event->getResponse());
    }

    /**
     * @return void
     * @covers Backend\Base\Listener\BaseListener::coreResultEvent
     */
    public function testResponseResultEvent()
    {
        $result = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');

        $event = $this->getMock(
            'Backend\Core\Event\ResultEvent',
            null,
            array($result)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $listener = new BaseListener($this->container);
        $listener->coreResultEvent($event);

        $this->assertSame($result, $event->getResponse());
    }

    /**
     * @return void
     * @covers Backend\Base\Listener\BaseListener::coreResultEvent
     * @covers Backend\Base\Listener\BaseListener::callbackFormat
     */
    public function testResultEventWithFormatting()
    {
        $result = new \stdClass;

        $event = $this->getMock(
            'Backend\Core\Event\ResultEvent',
            null,
            array($result)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $request = $this->getMockForAbstractClass(
            '\Backend\Interfaces\RequestInterface'
        );
        $this->container->set('request', $request);

        $callback = $this->getMockForAbstractClass(
            '\Backend\Interfaces\CallbackInterface'
        );
        $callback
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('actionFormat'));
        $callback
            ->expects($this->once())
            ->method('execute')
            ->with(array($result))
            ->will($this->returnValue($result));

        $this->container->set('callback', $callback);

        $response = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');

        $formatter = $this->getMockForAbstractClass('\Backend\Interfaces\FormatterInterface');
        $formatter
            ->expects($this->once())
            ->method('transform')
            ->with($result)
            ->will($this->returnValue($response));

        $this->container->set('formatter', $formatter);

        $listener = new BaseListener($this->container);
        $listener->coreResultEvent($event);

        $this->assertSame($response, $event->getResponse());
    }

    /**
     * @return void
     * @covers Backend\Base\Listener\BaseListener::coreResultEvent
     */
    public function testAddsBufferResultEvent()
    {
        $result = new \stdClass;

        $event = $this->getMock(
            'Backend\Core\Event\ResultEvent',
            null,
            array($result)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $request = $this->getMockForAbstractClass(
            '\Backend\Interfaces\RequestInterface'
        );
        $this->container->set('request', $request);

        $callback = $this->getMockForAbstractClass(
            '\Backend\Interfaces\CallbackInterface'
        );
        $callback
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('actionFormat'));
        $callback
            ->expects($this->once())
            ->method('execute')
            ->with(array($result))
            ->will($this->returnValue($result));

        $this->container->set('callback', $callback);

        $response = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');

        $formatter = $this->getMock(
            '\Backend\Interfaces\FormatterInterface',
            array('setValue', 'transform')
        );
        $formatter
            ->expects($this->once())
            ->method('setValue')
            ->with('buffered');

        $response = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');
        $formatter
            ->expects($this->once())
            ->method('transform')
            ->with($result)
            ->will($this->returnValue($response));

        $this->container->set('formatter', $formatter);

        ob_start();
        echo 'buffer';
        $listener = new BaseListener($this->container);
        $listener->coreResultEvent($event);
    }

    /**
     * @return void
     * @covers Backend\Base\Listener\BaseListener::coreResultEvent
     */
    public function testDoesNotAddGZippedBufferResultEvent()
    {
        $result = new \stdClass;

        $event = $this->getMock(
            'Backend\Core\Event\ResultEvent',
            null,
            array($result)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $request = $this->getMockForAbstractClass(
            '\Backend\Interfaces\RequestInterface'
        );
        $this->container->set('request', $request);

        $callback = $this->getMockForAbstractClass(
            '\Backend\Interfaces\CallbackInterface'
        );
        $callback
            ->expects($this->once())
            ->method('getMethod')
            ->will($this->returnValue('actionFormat'));
        $callback
            ->expects($this->once())
            ->method('execute')
            ->with(array($result))
            ->will($this->returnValue($result));

        $this->container->set('callback', $callback);

        $response = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');

        $formatter = $this->getMock(
            '\Backend\Interfaces\FormatterInterface',
            array('setValue', 'transform')
        );
        $formatter
            ->expects($this->never())
            ->method('setValue');

        $response = $this->getMockForAbstractClass('\Backend\Interfaces\ResponseInterface');
        $formatter
            ->expects($this->once())
            ->method('transform')
            ->with($result)
            ->will($this->returnValue($response));

        $this->container->set('formatter', $formatter);

        ob_start('ob_gzhandler');
        $listener = new BaseListener($this->container);
        $listener->coreResultEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreExceptionEvent
     * @return void
     */
    public function testUnauthorizedWithNoRedirectOnExceptionEvent()
    {
        $requestContext = $this->getMock('\Backend\Interfaces\RequestContextInterface');
        $requestContext
            ->expects($this->any())
            ->method('getLink')
            ->will($this->returnValue('http://backend-php.net'));
        $this->container->set('request_context', $requestContext);

        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('notice');
        $this->container->set('logger', $logger);

        $exception = new \Exception('Message', 401);

        $event = $this->getMock(
            'Backend\Core\Event\ExceptionEvent',
            array('getException'),
            array($exception)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');
        $event
            ->expects($this->once())
            ->method('getException')
            ->will($this->returnValue($exception));

        $listener = new BaseListener($this->container);
        $listener->coreExceptionEvent($event);

        $response = $event->getResponse();
        $this->assertInstanceOf('\Backend\Core\Response', $response);
        $this->assertEquals(302, $response->getStatusCode());
        $this->assertEquals('http://backend-php.net', $response->getHeader('Location'));
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreExceptionEvent
     * @return void
     */
    public function testUnauthorizedWithRedirectOnExceptionEvent()
    {
        $requestContext = $this->getMock('\Backend\Interfaces\RequestContextInterface');
        $requestContext
            ->expects($this->any())
            ->method('getLink')
            ->will($this->returnValue('http://backend-php.net'));
        $this->container->set('request_context', $requestContext);

        $this->container->setParameter('unauthorized.redirect', '/test');

        $exception = new \Exception('Message', 401);

        $event = $this->getMock(
            'Backend\Core\Event\ExceptionEvent',
            array(),
            array($exception)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');
        $event
            ->expects($this->once())
            ->method('getException')
            ->will($this->returnValue($exception));
        $event
            ->expects($this->once())
            ->method('setResponse')
            ->with($this->isInstanceOf('\Backend\Core\Response'));

        $listener = new BaseListener($this->container);
        $listener->coreExceptionEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreExceptionEvent
     * @return void
     */
    public function testLoggingOnExceptionEvent()
    {
        $exception = new \Exception('Message', 500);

        $event = $this->getMock(
            'Backend\Core\Event\ExceptionEvent',
            array(),
            array($exception)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');
        $event
            ->expects($this->once())
            ->method('getException')
            ->will($this->returnValue($exception));

        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('crit');

        $this->container->set('logger', $logger);

        $listener = new BaseListener($this->container);
        $listener->coreExceptionEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreShutdownEvent
     * @return void
     */
    public function testErrorCheckOnShutdownEvent()
    {
        $event = $this->getMock('\Symfony\Component\EventDispatcher\Event');

        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('crit');

        $this->container->set('logger', $logger);

        $error = array(
            'type' => E_ERROR,
            'message' => 'Test Base Shutdown Event'
        );

        $listener = new BaseListener($this->container);
        $listener->coreShutdownEvent($event, $error);
    }
}
