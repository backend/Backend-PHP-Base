<?php
/**
 * File defining Base\Controllers\ExamplesController
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
 * An example controller containing some sample code
 *
 * @category   Backend
 * @package    Base
 * @subpackage Controllers
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class ExamplesController extends \Backend\Base\Controller
{
    /**
     * The home callback.
     *
     * @return string Some string
     */
    public function homeAction()
    {
        $this->flash('success', 'This is a success flash message');
        $this->flash('info', 'This is a info flash message');
        $this->flash('warning', 'This is a warning flash message');
        $this->flash('error', 'This is a error flash message');
        return 'Some string';
    }

    /**
     * A callback showing how to render a template
     *
     * @param mixed $result The result returned from homeAction
     *
     * @return \Backend\Base\Utilities\Renderable
     */
    public function homeHtml($result)
    {
        return $this->render('home', array('result' => $result));
    }

    /**
     * A callback showing how to render a template
     *
     * @param mixed $result The result returned from homeAction
     *
     * @return \Backend\Base\Utilities\Renderable
     */
    public function homeCli($result)
    {
        return $result;
    }

    /**
     * A callback showing the parameter functionality
     *
     * @param mixed $id      An id
     * @param mixed $another An optional parameter
     *
     * @return null
     */
    public function paramsAction($id, $another = false)
    {
        return 'ID: ' . $id . ', Another: ' . var_export($another, true);
    }

    /**
     * A callback who'se route is generated using the controllers array.
     *
     * @return string
     */
    public function listAction()
    {
        return 'This is the List Action.';
    }
}
