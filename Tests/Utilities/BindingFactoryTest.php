<?php
/**
 * File defining \Backend\Base\Tests\Utilities\BindingFactoryTest
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
namespace Backend\Base\Tests\Utilities;

use Backend\Base\Utilities\BindingFactory;

/**
 * Class to test the \Backend\Base\Utilities\BindingFactory class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class BindingFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the Bindings setter and getter.
     *
     * @return void
     */
    public function testBindingsAccessors()
    {
        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        // Recreate the object so that we don't test the constructor
        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $this->assertSame($factory, $factory->setBindings($bindings));
        $this->assertSame($bindings, $factory->getBindings());
    }

    /**
     * Test the Connections setter and getter.
     *
     * @return void
     */
    public function testConnectionsAccessors()
    {
        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        // Recreate the object so that we don't test the constructor
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $this->assertSame($factory, $factory->setConnections($connections));
        $this->assertSame($connections, $factory->getConnections());
    }

    /**
     * Test the Binding (singular) setter and getter.
     *
     * @return void
     */
    public function testBindingAccessors()
    {
        $binding = array('some' => 'value');
        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $bindings
            ->expects($this->once())
            ->method('set')
            ->with('testBinding', $binding);
        $bindings
            ->expects($this->once())
            ->method('has')
            ->with('testBinding')
            ->will($this->returnValue(true));
        $bindings
            ->expects($this->once())
            ->method('get')
            ->with('testBinding')
            ->will($this->returnValue($binding));

        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        // Recreate the object so that we don't test the constructor
        $this->assertSame($factory, $factory->setBinding('testBinding', $binding));
        $this->assertEquals($binding, $factory->getBinding('testBinding'));
    }

    /**
     * Test the Connection (singular) setter and getter.
     *
     * @return void
     */
    public function testConnectionAccessors()
    {
        $connection = array('some' => 'value');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections
            ->expects($this->once())
            ->method('set')
            ->with('testConnection', $connection);
        $connections
            ->expects($this->once())
            ->method('has')
            ->with('testConnection')
            ->will($this->returnValue(true));
        $connections
            ->expects($this->once())
            ->method('get')
            ->with('testConnection')
            ->will($this->returnValue($connection));

        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        // Recreate the object so that we don't test the constructor
        $this->assertSame($factory, $factory->setConnection('testConnection', $connection));
        $this->assertEquals($connection, $factory->getConnection('testConnection'));
    }

    /**
     * Test undefined connection / binding
     *
     * @return void
     */
    public function testUndefinedConnectionOrBinding()
    {
        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        $this->assertNull($factory->getBinding('non-existant'));
        $this->assertNull($factory->getConnection('non-existant'));
    }

    /**
     * Test the constructor.
     *
     * @return void
     */
    public function testConstructor()
    {
        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        $this->assertSame($bindings, $factory->getBindings());
        $this->assertSame($connections, $factory->getConnections());
    }

    /**
     * Test a unconfigured binding.
     *
     * @return void
     * @expectedException \Backend\Core\Exceptions\ConfigException
     * @expectedExceptionMessage No binding setup
     */
    public function testUnconfiguredBinding()
    {
        require_once __DIR__ . '/../auxiliary/TestBinding.php';

        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        $binding = array(
            'type' => '\TestBinding',
        );
        $connection = array(
            'driver' => 'test',
        );
        $bindings
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValue(false));

        $actual = $factory->build('TestModel');
    }

    /**
     * Test a misconfigured Binding
     *
     * @return void
     * @expectedException \Backend\Core\Exceptions\ConfigException
     * @expectedExceptionMessage Missing Binding Type
     */
    public function testFaultyBinding()
    {
        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        $binding = array(
            'faulty' => 'binding'
        );
        $connection = array(
            'driver' => 'test',
        );
        $bindings
            ->expects($this->any())
            ->method('has')
            ->will($this->returnValue(true));
        $bindings
            ->expects($this->once())
            ->method('get')
            ->with('\TestModel')
            ->will($this->returnValue($binding));

        $actual = $factory->build('TestModel');
    }

    /**
     * Test a missing connection.
     *
     * @return void
     * @expectedException \Backend\Core\Exceptions\ConfigException
     * @expectedExceptionMessage Could not find Binding Connection
     */
    public function testMissingConnection()
    {
        require_once __DIR__ . '/../auxiliary/TestBinding.php';

        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        $binding = array(
            'type' => '\TestBinding',
        );
        $connection = array(
            'driver' => 'test',
        );
        $bindings
            ->expects($this->any())
            ->method('has')
            ->with('\TestModel')
            ->will($this->returnValue(true));
        $bindings
            ->expects($this->once())
            ->method('get')
            ->with('\TestModel')
            ->will($this->returnValue($binding));
        $connections
            ->expects($this->any())
            ->method('has')
            ->with('default')
            ->will($this->returnValue(false));

        $actual = $factory->build('TestModel');
    }

    /**
     * Test a successful build method.
     *
     * @return void
     */
    public function testSuccessfulBuild()
    {
        require_once __DIR__ . '/../auxiliary/TestBinding.php';

        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        $binding = array(
            'type' => '\TestBinding',
        );
        $connection = array(
            'driver' => 'test',
        );
        $bindings
            ->expects($this->any())
            ->method('has')
            ->with('\TestModel')
            ->will($this->returnValue(true));
        $bindings
            ->expects($this->once())
            ->method('get')
            ->with('\TestModel')
            ->will($this->returnValue($binding));
        $connections
            ->expects($this->any())
            ->method('has')
            ->with('default')
            ->will($this->returnValue(true));
        $connections
            ->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($connection));

        $actual = $factory->build('TestModel');
        $this->assertInstanceOf('\Backend\Interfaces\BindingInterface', $actual);
    }

    /**
     * Test getting the default Binding
     *
     * @return void
     */
    public function testDefaultBinding()
    {
        require_once __DIR__ . '/../auxiliary/TestBinding.php';

        $bindings = $this->getMock('Backend\Interfaces\ConfigInterface');
        $connections = $this->getMock('Backend\Interfaces\ConfigInterface');
        $factory = new BindingFactory($bindings, $connections);

        $binding = array(
            'type' => '\TestBinding',
        );
        $connection = array(
            'driver' => 'test',
        );
        $bindings
            ->expects($this->at(0))
            ->method('has')
            ->with('\TestModel')
            ->will($this->returnValue(false));
        $bindings
            ->expects($this->at(1))
            ->method('has')
            ->with('default')
            ->will($this->returnValue(true));
        $bindings
            ->expects($this->at(2))
            ->method('has')
            ->with('\TestModel')
            ->will($this->returnValue(false));
        $bindings
            ->expects($this->at(3))
            ->method('has')
            ->with('default')
            ->will($this->returnValue(true));
        $bindings
            ->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($binding));
        $connections
            ->expects($this->any())
            ->method('has')
            ->with('default')
            ->will($this->returnValue(true));
        $connections
            ->expects($this->once())
            ->method('get')
            ->with('default')
            ->will($this->returnValue($connection));

        $actual = $factory->build('TestModel');
        $this->assertInstanceOf('\Backend\Interfaces\BindingInterface', $actual);
    }
}
