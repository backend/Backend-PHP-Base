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
use \Backend\Core\Response;
use \Backend\Base\Utilities\Renderable;
/**
 * The Crud Controller is a Decorator that provides basic CRUD functionality to controllers
 *
 * Executing GET requests on the following special resources modifies the default REST behaviour
 * * <controller>/<id>/input Return the inputs required to create or update an entity.
 *
 * The execution of the different functions can be overridden by defining the
 * Action function (eg. readAction) in the decorated object, or modified by
 * defining Prepare (readPrepare) and / or Modify (readModift) methods. If the
 * Prepare or Modify methods return an instance of \Backend\Core\Response, that
 * response is immediately returned.
 *
 * @todo       Make sure we follow http://i.stack.imgur.com/whhD1.png
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
     * CRUD Create functionality for controllers.
     *
     * @return \Backend\Core\Response A Created / 201 Response
     */
    public function createAction()
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'createAction'))) {
            return $this->object->createAction();
        }

        $data = $this->getRequest()->getPayload();
        if (is_callable(array($this->object, 'createPrepare'))) {
            $data = $this->object->createPrepare($data);
            if ($data instanceof Response) {
                return $data;
            }
        }
        $modelName = \Backend\Core\Controller::getModelName($this->object);
        $model     = call_user_func(array($modelName, 'create'), $data);
        if (is_callable(array($this->object, 'createModify'))) {
            $model = $this->object->createModify($model);
            if ($model instanceof Response) {
                return $model;
            }
        }
        return new Response($model, 201);
    }

    /**
     * The HTML function for the create Action.
     *
     * It creates a Redirect Response to the created resource.
     *
     * @param mixed $result The result from the createAction
     *
     * @return \Backend\Core\Response A Redirect to the created resource
     */
    public function createHtml($result)
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'createHtml'))) {
            return $this->object->createHtml($result);
        }
        if ($result instanceof Response && $result->getStatusCode() == 201) {
            //TODO Fix this redirect. We will need to reverse Routes
            return $this->redirect(SITE_LINK);
        }
    }

    /**
     * CRUD Read functionality for controllers.
     *
     * @param mixed $identifier The identifier. Set to 0 to reference the whole collection
     *
     * @return ModelInterface The model
     */
    public function readAction($identifier)
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'readAction'))) {
            return $this->object->readAction();
        }

        if (is_callable(array($this->object, 'readPrepare'))) {
            $result = $this->object->readPrepare($identifier);
            if ($result instanceof Response) {
                return $result;
            }
        }
        $model = $this->getModel($identifier);
        if (is_null($model->getId())) {
            //the specified Resource doesn't exist
            return new Response('Not Found', 404);
        }
        if (is_callable(array($this->object, 'readModify'))) {
            $result = $this->object->readModify($model);
            if ($result instanceof Response) {
                return $result;
            }
        }
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
        //Check for already defined function
        if (is_callable(array($this->object, 'readHtml'))) {
            return $this->object->readHtml($result);
        }

        if ($result instanceof Response) {
            return $result;
        }
        $modelName = explode('\\', \Backend\Core\Controller::getModelName($this->object));
        $modelName = end($modelName);
        return new Renderable('crud/display', array('result' => $result->getOriginalObject(), 'component' => $modelName));
    }

    /**
     * An Extension of the CRUD Read function to list a resource
     *
     * @return array An array of the Resource
     */
    public function listAction()
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'listAction'))) {
            return $this->object->listAction();
        }

        $modelName = \Backend\Core\Controller::getModelName($this->object);
        $result = call_user_func(array($modelName, 'findAll'));
        if (is_callable(array($this->object, 'readModify'))) {
            $result = $this->object->readModify($result);
            if ($result instanceof Response) {
                return $result;
            }
        }
        return $result;
    }

    /**
     * The HTML function for the List function
     *
     * @param array $result The result from the List function
     *
     * @return string The rendered Model
     */
    public function listHtml($result)
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'listHtml'))) {
            return $this->object->listHtml($result);
        }

        $modelName = explode('\\', \Backend\Core\Controller::getModelName($this->object));
        $modelName = end($modelName);
        return new Renderable('crud/list', array('list' => $result, 'component' => $modelName));
    }

    /**
     * CRUD Update functionality for controllers.
     *
     * @param mixed $identifier The identifier. Set to 0 to reference the whole collection
     *
     * @return \Backend\Core\Response A No Content / 204 Response if successful
     */
    public function updateAction($identifier)
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'updateAction'))) {
            return $this->object->updateAction();
        }

        $model = $this->getModel($identifier);
        if (is_null($model)) {
            //the specified Resource doesn't exist
            return new Response('Not Found', 404);
        }

        $data = $this->getRequest()->getPayload();
        if (is_callable(array($this->object, 'updatePrepare'))) {
            $data = $this->object->updatePrepare($data);
            if ($data instanceof Response) {
                return $data;
            }
        }
        //Populate the model and update
        $model->populate($data);
        $model->update();
        if (is_callable(array($this->object, 'updateModify'))) {
            $model = $this->object->updateModify($model);
            if ($model instanceof Response) {
                return $model;
            }
        }
        return new Response($model, 204);
    }

    /**
     * The HTML function for the update Action
     *
     * @param mixed $result The result from the updateAction
     *
     * @return \Backend\Core\Response A Redirect to the updated resource
     */
    public function updateHtml($result)
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'updateHtml'))) {
            return $this->object->updateHtml($result);
        }
        if ($result instanceof Response && $result->getStatusCode() == 204) {
            //TODO Fix this redirect. We will need to reverse Routes
            return $this->redirect(SITE_LINK);
        }
    }

    /**
     * CRUD Delete functionality for controllers.
     *
     * @param mixed $identifier The identifier. Set to 0 to reference the whole collection
     *
     * @return \Backend\Core\Response A No Content / 204 Response if successful
     */
    public function deleteAction($identifier)
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'deleteAction'))) {
            return $this->object->deleteAction($identifier);
        }

        if (is_callable(array($this->object, 'deletePrepare'))) {
            $identifier = $this->object->deletePrepare($identifier);
            if ($identifier instanceof Response) {
                return $identifier;
            }
        }

        $model = $this->getModel($identifier);
        if (is_null($model)) {
            //the specified Resource doesn't exist
            return new Response('Not Found', 404);
        }

        $model->destroy();
        if (is_callable(array($this->object, 'deleteModify'))) {
            $model = $this->object->deleteModify($model);
            if ($model instanceof Response) {
                return $model;
            }
        }
        return new Response($model, 204);
    }

    /**
     * The HTML function for the delete Action
     *
     * @param mixed $result The result from the deleteAction
     *
     * @return \Backend\Core\Response A Redirect to the resource collection
     */
    public function deleteHtml($result)
    {
        //Check for already defined function
        if (is_callable(array($this->object, 'deleteHtml'))) {
            return $this->object->deleteHtml($result);
        }
        if ($result instanceof Response && $result->getStatusCode() == 204) {
            //TODO Fix this redirect. We will need to reverse Routes
            return $this->redirect(SITE_LINK);
        }
    }
}
