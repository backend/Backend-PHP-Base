<?php
/**
 * File defining Backend\Base\ModelTest
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
use \Backend\Base\Model;
require_once __DIR__ . '/auxiliary/TestModel.php';
/**
 * Class to test the \Backend\Base\Model class
 *
 * @category Backend
 * @package  BaseTests
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class ModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that the magic get method uses the accessor.
     *
     * @return void
     */
    public function testMagic()
    {
        $model = new \TestModel;
        $model->property = 'value';
        $this->assertEquals('value', $model->property);
    }

    /**
     * Test that the magic set method uses the accessor.
     *
     * @return void
     */
    public function testMagicThroughAccessor()
    {
        $model = new \TestModel;
        $model->accessor = 'value';
        $this->assertEquals('value', $model->accessor);
    }

    /**
     * Test getting an undefined property
     *
     * @return void
     * @expectedException \ErrorException
     * @expectedExceptionMessage Undefined property
     */
    public function testGetUndefinedProperty()
    {
        $model = new \TestModel;
        var_dump($model->some_property);
    }

    /**
     * Test setting an undefined property
     *
     * @return void
     * @expectedException \ErrorException
     * @expectedExceptionMessage Trying to set Undefined property
     */
    public function testSetUndefinedProperty()
    {
        $model = new \TestModel;
        $model->some_property = 'value';
    }

    /**
     * Test the populate method.
     *
     * @return void
     */
    public function testPopulateAndGetProperties()
    {
        $array = array(
            'property' => 'property',
            'accessor' => 'accessor',
        );
        $model = new \TestModel;
        $model->populate($array);
        $this->assertSame($array, $model->getProperties());
    }

    /**
     * Test Exception on populate.
     *
     * @return void
     * @expectedException \ErrorException
     * @expectedExceptionMessage Undefined property
     */
    public function testExceptionOnPopulate()
    {
        $array = array('some_value' => 'value');
        $model = new \TestModel;
        $model->populate($array);
    }

    /**
     * Test the toJson Method.
     *
     * @return void
     */
    public function testToJson()
    {
        $model = new \TestModel;
        $model->property = 'value';
        $json = json_encode(array('property' => 'value', 'accessor' => null));
        $this->assertEquals($json, $model->toJson());
    }

    /**
     * Test invalid JSON.
     *
     * @return void
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Json Encoding Error
     */
    public function testInvalidJson()
    {
        $model = new \TestModel;
        $model->property = "\xB1\x31";
        $model->toJson();
    }
}
