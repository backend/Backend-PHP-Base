<?php
/**
 * File defining TestModel.
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    CoreTests
 * @subpackage Auxiliary
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */

/**
 * Class to test the Model accessors
 *
 * @category   Backend
 * @package    CoreTests
 * @subpackage Utilities
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class TestModel extends \Backend\Base\Model
{
    protected $property;
    protected $accessor;
    protected $_hidden;

    public function getAccessor()
    {
        return $this->accessor;
    }

    public function setAccessor($value)
    {
        $this->accessor = $value;
    }
}
