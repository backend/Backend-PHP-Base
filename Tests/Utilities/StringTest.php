<?php
/**
 * File defining \Backend\Base\Tests\Utilities\StringTest
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
use \Backend\Base\Utilities\String;

/**
 * Class to test the \Backend\Base\Utilities\String class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data for the testCamelCase method.
     *
     * @return array
     */
    public function dataCamelCase()
    {
        $result = array();
        $result[] = array('some day', 'SomeDay');
        $result[] = array('UFO Village', 'UFOVillage');
        $result[] = array('strange-function', 'StrangeFunction');
        $result[] = array('test.class', 'TestClass');
        $result[] = array('some_string', 'SomeString');
        return $result;
    }

    /**
     * Test the camel case method.
     *
     * @param string $string   The string to transform.
     * @param string $expected The expected string after transformation.
     *
     * @return void
     * @dataProvider dataCamelCase
     */
    public function testCamelCase($string, $expected)
    {
        $string = new String($string);
        $this->assertEquals($expected, (string) $string->camelCase());
    }

    /**
     * Data for the testUnCamel method.
     *
     * @return array
     */
    public function dataUnCamel()
    {
        $result = array();
        $result[] = array('SomeDay', ' ', 'some day');
        $result[] = array('UFOVillage', ' ', 'ufo village');
        $result[] = array('StrangeFunction', '-', 'strange-function');
        $result[] = array('TestClass', '.', 'test.class');
        $result[] = array('SomeString', '_', 'some_string');
        return $result;
    }

    /**
     * Test the unCamel method.
     *
     * @param string $string    The string to transform.
     * @param string $separator The separator.
     * @param string $expected  The expected string after transformation.
     *
     * @return void
     * @dataProvider dataUnCamel
     */
    public function testUnCamel($string, $separator, $expected)
    {
        $string = new String($string);
        $this->assertEquals($expected, (string) $string->unCamel($separator));
    }

    /**
     * Data for the testSingularize method.
     *
     * @return array
     */
    public function dataSingularAndPlural()
    {
        $result = array();
        $result[] = array('move', 'moves');
        $result[] = array('sheep', 'sheep');
        $result[] = array('person', 'people');
        $result[] = array('index', 'indices');
        $result[] = array('cat', 'cats');
        $result[] = array('thief', 'thieves');
        $result[] = array('octopus', 'octopi');
        $result[] = array('regular', 'regulars');
        return $result;
    }

    /**
     * Test the singularize method.
     *
     * @param string $singular The string to transform.
     * @param string $plural   The expected string after transformation.
     *
     * @return void
     * @dataProvider dataSingularAndPlural
     */
    public function testSingularize($singular, $plural)
    {
        $plural = new String($plural);
        $this->assertEquals($singular, (string) $plural->singularize());
    }

    /**
     * Test the pluralize method.
     *
     * @param string $plural   The expected string after transformation.
     * @param string $singular The string to transform.
     *
     * @return void
     * @dataProvider dataSingularAndPlural
     */
    public function testPluralize($singular, $plural)
    {
        $singular = new String($singular);
        $this->assertEquals($plural, (string) $singular->pluralize());
    }
}
