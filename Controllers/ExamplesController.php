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
use \Backend\Base\Utilities\Renderable;
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
class ExamplesController extends \Backend\Core\Controller
{
    /**
     * Contructor for the Controller.
     *
     * Decorate this Controller with CRUD functionality
     *
     * @param \Backend\Core\Request $request The request object for the execution of the action
     */
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    /**
     * The home function
     *
     * @return string Some string
     */
    public function homeAction()
    {
        return 'Some string';
    }

    /**
     * A function showing how to render a template
     *
     * @param mixed $result The result returned from homeAction
     *
     * @return \Backend\Base\Utilities\Renderable
     */
    public function homeHtml($result)
    {
        return new Renderable('home', array('result' => $result));
    }

    /**
     * A function showing how to render a template
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
     * A function showing the parameter functionality
     *
     * @param mixed $id      An id
     * @param mixed $another An optional parameter
     *
     * @return null
     */
    public function paramsAction($id, $another = false)
    {
        var_dump($id, $another);
    }

}
