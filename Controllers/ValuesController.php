<?php
/**
 * File defining Base\Controllers\ValuesController
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Controllers
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Controllers;
/**
 * A simple ModelController showing how to use Bindings and mapping REST actions
 * to CRUD methods.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Controllers
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class ValuesController extends \Backend\Base\Controllers\ModelController
{
    public function listAction()
    {
        try {
            return parent::listAction();
        } catch (\RuntimeException $e) {
            if (substr($e->getMessage(), 0, strlen('Query Error: no such table')) === 'Query Error: no such table') {
                $result = $this->getBinding()->exec(
                    'CREATE TABLE `values` (`id` INTEGER PRIMARY KEY, `name` TEXT, `value` TEXT)'
                );

                return $this->redirect($this->getRequest()->getUrl() . '/value');
            }
            throw $e;
        }
    }
}
