<?php
/**
 * File defining Backend\Base\Decorators\CrudController
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Interfaces
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Decorators;
use \Backend\Core\Decorators\Decorator;
/**
 * The Crud Controller is a Decorator that provides basic CRUD functionality to controllers
 *
 * Executing GET requests on the following special resources modifies the default REST behaviour
 * * <controller>/<id>/input Return the inputs required to create or update an entity.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Interfaces
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class CrudController extends Decorator
{
    /**
     * CRUD Read functionality for controllers.
     *
     * @param mixed $identifier The identifier. Set to 0 to reference the collection
     *
     * @return ModelInterface The model
     */
    public function readAction($identifier)
    {
        $model = $this->getModel();
        if (is_null($model)) {
            throw new \Exception('Could not find specified Model');
        }
        $model->read($identifier);
        return $model;
    }

    /**
     * The HTML function for the read Action
     *
     * @param mixed $result The result from the readAction
     *
     * @return string The rendered Model
     */
    public function readHtml($result)
    {
        $view = $view = \Backend\Core\Application::getTool('View');
        $arguments = $this->getRequest()->getPayload();
        if (count($arguments) >= 1 && $arguments[0] == 'input') {
            $template = 'crud/form';
            $view->bind('title', 'Update ' . $model->getName());
        } else {
            $template = 'crud/display';
            $view->bind('title', 'Display ' . $model->getName());
        }
        return new Renderable($template, array('model' => $model));
    }

    public function listAction()
    {
        $modelName = \Backend\Core\Controller::getModelName($this->object);
        return call_user_func(array($modelName, 'findAll'));
    }

    public function listHtml($result)
    {
        return new Renderable('crud/list', array('list' => $result));
    }
}
