<?php
namespace Backend\Base\Tests\Listener;

use Backend\Base\Listener\BaseListener;
use Backend\Core\Utilities\Config;
use Backend\Core\Utilities\DependencyInjectionContainer;

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

        $event = $this->getMock(
            'Backend\Core\Event\CallbackEvent',
            null,
            array($callback, $this->container)
        );
        $event
            ->expects($this->never())
            ->method('stopPropagation');

        $listener = new BaseListener($this->container);
        $listener->coreInitEvent($event);
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreExceptionEvent
     * @return void
     */
    public function testUnauthorizedOnExceptionEvent()
    {
        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('notice');

        $this->container->set('logger', $logger);

        $exception = new \Exception('Message', 401);

        $event = $this->getMock(
            'Backend\Core\Event\ExceptionEvent',
            null,
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
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreExceptionEvent
     * @return void
     */
    public function testLoggingOnExceptionEvent()
    {
    }

    /**
     * @covers Backend\Base\Listener\BaseListener::coreShutdownEvent
     * @return void
     */
    public function testErrorCheckOnShutdownEvent()
    {
    }
}