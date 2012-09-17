<?php
/**
 * File defining \Backend\Base\Tests\Utilities\RenderableTest
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
use Backend\Base\Utilities\Renderable;

/**
 * Class to test the \Backend\Base\Utilities\Renderable class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class RenderableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the Template getter and setter
     *
     * @return void
     */
    public function testTemplateAccessors()
    {
        $template = 'template.twig';
        $render   = $this->getMock('\Backend\Interfaces\RenderInterface');
        $renderable = new Renderable($render, 'constructor');
        $this->assertSame($renderable, $renderable->setTemplate($template));
        $this->assertSame($template, $renderable->getTemplate());
    }

    /**
     * Test the Values getter and setter
     *
     * @return void
     */
    public function testValuesAccessors()
    {
        $values = array('test' => 'set');
        $render   = $this->getMock('\Backend\Interfaces\RenderInterface');
        $renderable = new Renderable($render, 'constructor', array());
        $this->assertSame($renderable, $renderable->setValues($values));
        $this->assertSame($values, $renderable->getValues());
    }

    /**
     * Test Adding Values.
     *
     * @return void
     */
    public function testAddValue()
    {
        $render = $this->getMock('\Backend\Interfaces\RenderInterface');
        $initial = array('one' => 'values', 'three' => 'third');
        $renderable = new Renderable($render, 'constructor', $initial);
        $values = array('one' => 'first', 'two' => 'second');
        $renderable->addValues($values);

        $expected = array_merge($initial, $values);
        $this->assertEquals($expected, $renderable->getValues());
    }

    /**
     * Test Setting a Value.
     *
     * @return void
     */
    public function testSetValue()
    {
        $render = $this->getMock('\Backend\Interfaces\RenderInterface');
        $renderable = new Renderable($render, 'constructor');
        $renderable->setValue('one', 'set');
        $this->assertEquals(array('one' => 'set'), $renderable->getValues());
        $renderable->setValue('new', 'value');
        $this->assertEquals(array('one' => 'set', 'new' => 'value'), $renderable->getValues());
    }

    /**
     * Test the rendering.
     *
     * @return void
     */
    public function testRender()
    {
        $render = $this->getMock('\Backend\Interfaces\RenderInterface');
        $render
            ->expects($this->once())
            ->method('file')
            ->with('constructor', array('some' => 'value'))
            ->will($this->returnValue('Rendered'));
        $renderable = new Renderable($render, 'constructor', array('some' => 'value'));
        $this->assertEquals('Rendered', (string) $renderable);
    }

    /**
     * Test a rendering exception.
     *
     * @return void
     */
    public function testRenderError()
    {
        $render = $this->getMock('\Backend\Interfaces\RenderInterface');
        $render
            ->expects($this->once())
            ->method('file')
            ->with('constructor', array('some' => 'value'))
            ->will($this->throwException(new \Exception));
        $renderable = new Renderable($render, 'constructor', array('some' => 'value'));
        $this->assertStringStartsWith('There was an error parsing constructor', (string) $renderable);
    }
}
