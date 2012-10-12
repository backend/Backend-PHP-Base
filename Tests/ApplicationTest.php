<?php
/**
 * File defining Backend\Base\ApplicationTest
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
use Backend\Core\Utilities\Config;
use Backend\Base\Application;
use Backend\Core\Utilities\DependencyInjectionContainer;
/**
 * Class to test the \Backend\Base\Application class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    protected $request = null;

    protected $container = null;

    protected $application = null;

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
        $config = new Config($parser, __DIR__ . '/auxiliary/configs/application.testing.yml');
        $this->container   = new DependencyInjectionContainer($config);
        $this->application = new Application($config, $this->container);
    }

    /**
     * Tear down the test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->application = null;
        $this->container = null;
    }

    /**
     * Test if callbacks are authenticated
     *
     * @return void
     */
    public function testAuthenticateCallback()
    {
        $callback = $this->getMockForAbstractClass('\Backend\Interfaces\CallbackInterface');
        $authenticator = $this->getMockForAbstractClass('\Backend\Interfaces\AuthenticatorInterface');
        $authenticator
            ->expects($this->once())
            ->method('check')
            ->with($callback);
        $this->container->set('authenticator', $authenticator);
        $callback = $this->application->transformCallback($callback);
        $this->assertInstanceOf('\Backend\Interfaces\CallbackInterface', $callback);
    }

    /**
     * Test if callbacks are logged
     *
     * @return void
     */
    public function testLogCallback()
    {
        $callback = $this->getMockForAbstractClass('\Backend\Interfaces\CallbackInterface');
        $formatter = $this->getMockForAbstractClass('\Backend\Interfaces\FormatterInterface');
        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->exactly(2))
            ->method('info');
        $this->container->set('logger', $logger);
        $callback = $this->application->transformCallback($callback);
        $this->assertInstanceOf('\Backend\Interfaces\CallbackInterface', $callback);
        $callback = $this->application->transformFormatCallback($callback, $formatter);
        $this->assertInstanceOf('\Backend\Interfaces\CallbackInterface', $callback);
    }

    /**
     * Test no formatter
     *
     * @return void
     * @expectedException \Backend\Core\Exception
     * @expectedExceptionMessage Unsupported format requested
     * @expectedExceptionCode 415
     */
    public function testNoFormatterThrowsException()
    {
        $result = $this->application->getFormatter();
    }

    /**
     * Test if we default to the HTML formatter
     *
     * @return void
     */
    public function testDefaultHtmlFormatter()
    {
        $formatter = $this->getMock('\Backend\Interfaces\FormatterInterface');
        $this->container->set('backend.base.formats.html', $formatter);
        $result = $this->application->getFormatter();
        $this->assertSame($formatter, $result);
    }

    /**
     * Test Unauthorized Exceptions
     *
     * @return void
     */
    public function testUnauthorizedException()
    {
        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('notice');
        $this->container->set('logger', $logger);

        $request = $this->getMockForAbstractClass('\Backend\Interfaces\RequestInterface');
        $request
            ->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('/secure'));
        $this->application->setRequest($request);

        $requestContext = $this->getMockForAbstractClass('\Backend\Interfaces\RequestContextInterface');
        $requestContext
            ->expects($this->once())
            ->method('getLink')
            ->will($this->returnValue('http://backend-php.net'));
        $this->container->set('request_context', $requestContext);

        $this->container->setParameter('unauthorized.redirect', '/');

        $exception = new \Exception('Unauthorized', 401);
        $result = $this->application->exception($exception, true);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
        $this->assertEquals(302, $result->getStatusCode());
        $this->assertContains('Location: http://backend-php.net/', $result->getHeaders());
    }

    /**
     * Test Uncaught Exceptions
     *
     * @return void
     */
    public function testUncaughtException()
    {
        $logger = $this->getMockForAbstractClass('\Backend\Interfaces\LoggerInterface');
        $logger
            ->expects($this->once())
            ->method('crit');
        $this->container->set('logger', $logger);

        $exception = new \Exception('Unhandled', 500);
        $result = $this->application->exception($exception, true);
        $this->assertInstanceOf('\Backend\Interfaces\ResponseInterface', $result);
    }
}
