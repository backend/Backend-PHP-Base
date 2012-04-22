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
 * defining Prepare (readPrepare) and / or Modify (readModify) methods. If the
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
        if ($object = $this->isCallable('createAction')) {
            return $object->createAction();
        }

        $data = $this->getRequest()->getPayload();
        if ($object = $this->isCallable('createPrepare')) {
            $data = $object->createPrepare($data);
            if ($data instanceof Response) {
                return $data;
            }
        }
        $modelName = \Backend\Core\Controller::getModelName($this->getOriginalObject());
        $model     = call_user_func(array($modelName, 'create'), $data);
        if ($object = $this->isCallable('createModify')) {
            $model = $object->createModify($model);
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
        if ($object = $this->isCallable('createHtml')) {
            return $object->createHtml($result);
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
        if ($object = $this->isCallable('readAction')) {
            return $object->readAction();
        }

        if ($object = $this->isCallable('readPrepare')) {
            $result = $object->readPrepare($identifier);
            if ($result instanceof Response) {
                return $result;
            }
        }
        $model = $this->getModel($identifier);
        if (is_null($model->getId())) {
            //the specified Resource doesn't exist
            return new Response('Not Found', 404);
        }
        if ($object = $this->isCallable('readModify')) {
            $result = $object->readModify($model);
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
        if ($object = $this->isCallable('readHtml')) {
            return $object->readHtml($result);
        }

        if ($result instanceof Response) {
            return $result;
        }
        $modelName = explode('\\', \Backend\Core\Controller::getModelName($this->getOriginalObject()));
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
        if ($object = $this->isCallable('listAction')) {
            return $object->listAction();
        }

        $modelName = \Backend\Core\Controller::getModelName($this->getOriginalObject());
        $result = call_user_func(array($modelName, 'findAll'));
        if ($object = $this->isCallable('listModify')) {
            $result = $object->listModify($result);
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
        if ($object = $this->isCallable('listHtml')) {
            return $object->listHtml($result);
        }

        $modelName = explode('\\', \Backend\Core\Controller::getModelName($this->getOriginalObject()));
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
        if ($object = $this->isCallable('updateAction')) {
            return $object->updateAction();
        }

        $model = $this->getModel($identifier);
        if (is_null($model)) {
            //the specified Resource doesn't exist
            return new Response('Not Found', 404);
        }

        $data = $this->getRequest()->getPayload();
        if ($object = $this->isCallable('updatePrepare')) {
            $data = $object->updatePrepare($data);
            if ($data instanceof Response) {
                return $data;
            }
        }
        //Populate the model and update
        $model->populate($data);
        $model->update();
        if ($object = $this->isCallable('updateModify')) {
            $model = $object->updateModify($model);
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
        if ($object = $this->isCallable('updateHtml')) {
            return $object->updateHtml($result);
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
        if ($object = $this->isCallable('deleteAction')) {
            return $object->deleteAction($identifier);
        }

        if ($object = $this->isCallable('deletePrepare')) {
            $identifier = $object->deletePrepare($identifier);
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
        if ($object = $this->isCallable('deleteModify')) {
            $model = $object->deleteModify($model);
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
        if ($object = $this->isCallable('deleteHtml')) {
            return $object->deleteHtml($result);
        }
        if ($result instanceof Response && $result->getStatusCode() == 204) {
            //TODO Fix this redirect. We will need to reverse Routes
            return $this->redirect(SITE_LINK);
        }
    }
}
