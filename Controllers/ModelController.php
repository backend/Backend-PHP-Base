<?php
/**
 * File defining Backend\Base\Controllers\ModelController
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
namespace Backend\Base\Controllers;
use Backend\Base\Controller;
use Backend\Interfaces\ResponseInterface;
use Backend\Base\Utilities\Renderable;
use Backend\Base\Utilities\String;
/**
 * The Model Controller provides basic CRUD functionality on Models to Controllers.
 *
 * Executing GET requests on the following special resources modifies the default REST behaviour
 *
 * @todo       <controller>/input Return the inputs required to create or update an entity.
 * @todo       <controller>/form and <controller>/<id>/form Return an HTML form to
 * create and update the model respectivel.
 * @todo       Make sure we follow http://i.stack.imgur.com/whhD1.png
 * @category   Backend
 * @package    Base
 * @subpackage Interfaces
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class ModelController extends Controller
{
    /**
     * Get a Form for the Model.
     *
     * @param mixed $id The identifier
     *
     * @return \Backend\Interfaces\ModelInterface|\Backend\Interfaces\ResponseInterface
     */
    public function formAction($id = null)
    {
        if ($id) {
            $model = $this->readAction($id);
        } else {
            $modelName = $this->getModelName();
            $model = new $modelName();
        }

        return $model;
    }

    /**
     * The HTML method for the form Action.
     *
     * @param mixed $result The result from the Action method.
     *
     * @return \Backend\Base\Utilities\Renderable
     */
    public function formHtml($result)
    {
        if ($result instanceof ResponseInterface) {
            return $result;
        }
        // TODO Check the current template folder for $modelName/form
        $component = explode('\\', get_class($result));
        $component = end($component);

        return $this->render('crud/form', array('model' => $result, 'component' => $component));
    }

    /**
     * CRUD Create functionality for controllers.
     *
     * @return \Backend\Interfaces\ResponseInterface A Created / 201 Response with a redirect
     * to the created resource.
     */
    public function createAction()
    {
        $data  = $this->getRequest()->getBody();
        $model = $this->getBinding()->create($data);
        $responseClass = $this->container->getParameter('response.class');
        $response = new $responseClass($model, 201);

        return $response;
    }

    /**
     * The HTML method for the create Action.
     *
     * @param mixed $result The result from the Action method.
     *
     * @return \Backend\Interfaces\ResponseInterface A Redirect to the created resource
     */
    public function createHtml($result)
    {
        if ($result instanceof ResponseInterface && $result->getStatusCode() == 201) {
            $redirect = $this->getRequest()->getUrl()  . '/' . $result->getBody()->getId();

            return $result
                ->setHeader('Location', $redirect)
                ->setStatusCode(302);
        }
        // We redirect back to the refer if the request failed
        return $this->redirect($this->getRequest()->getHeader('referer'));
    }

    /**
     * An Extension of the CRUD Read function to list a resource
     *
     * @return array An array of Models
     */
    public function listAction()
    {
        return $this->getBinding()->find();
    }

    /**
     * The HTML method for the list Action.
     *
     * @param array $result The result from the Action method.
     *
     * @return \Backend\Base\Utilities\Renderable
     */
    public function listHtml($result)
    {
        // TODO Check the current template folder for $modelName/list
        $component = explode('\\', self::getModelName());
        $component = end($component);

        return $this->render('crud/list', array('list' => $result, 'component' => $component));
    }

    /**
     * CRUD Read functionality for controllers.
     *
     * @param mixed $id The identifier
     *
     * @return \Backend\Interfaces\ModelInterface|\Backend\Interfaces\ResponseInterface
     */
    public function readAction($id)
    {
        $model = $this->getBinding()->read($id);
        if ($model === null) {
            // The specified Resource doesn't exist
            $responseClass = $this->container->getParameter('response.class');

            return new $responseClass('Not Found', 404);
        }

        return $model;
    }

    /**
     * The HTML method for the read Action.
     *
     * @param mixed $result The result from the Action method.
     *
     * @return \Backend\Base\Utilities\Renderable
     */
    public function readHtml($result)
    {
        if ($result instanceof ResponseInterface) {
            // Not found or similiar response
            return $result;
        }
        // TODO Check the current template folder for $modelName/display
        $component = explode('\\', get_class($result));
        $component = end($component);

        return $this->render('crud/display', array('result' => $result, 'component' => $component));
    }

    /**
     * CRUD Update functionality for controllers.
     *
     * @param mixed $id The identifier. @todo Set to 0 to reference the whole collection
     *
     * @return \Backend\Interfaces\ResponseInterface A No Content / 204 Response if successful.
     */
    public function updateAction($id)
    {
        $model = $this->readAction($id);
        if ($model instanceof ResponseInterface) {
            // The specified Resource doesn't exist
            return $model;
        }
        $data = $this->getRequest()->getBody();
        $model->populate($data);
        $this->getBinding()->update($model);
        $responseClass = $this->container->getParameter('response.class');

        return new $responseClass($model, 204);
    }

    /**
     * The HTML method for the update Action
     *
     * @param mixed $result The result from the Action method.
     *
     * @return \Backend\Core\Response A Redirect to the updated resource
     */
    public function updateHtml($result)
    {
        // We redirect back to the Resource
        $redirect = $this->getRequest()->getUrl();
        if ($result instanceof ResponseInterface && $result->getStatusCode() == 204) {
            return $result
                ->setHeader('Location', $redirect)
                ->setStatusCode(302);
        }

        return $this->redirect($redirect);
    }

    /**
     * CRUD Delete functionality for controllers.
     *
     * @param mixed $id The identifier. @todo Set to 0 to reference the whole collection
     *
     * @return \Backend\Core\Response A No Content / 204 Response if successful.
     */
    public function deleteAction($id)
    {
        $model = $this->readAction($id);
        if ($model instanceof ResponseInterface) {
            // The specified Resource doesn't exist
            return $model;
        }
        $this->getBinding()->delete($model);
        $responseClass = $this->container->getParameter('response.class');

        return new $responseClass('', 204);
    }

    /**
     * The HTML method for the delete Action
     *
     * @param mixed $result The result from the Action method.
     *
     * @return \Backend\Interfaces\ResponseInterface A Redirect to the resource collection
     */
    public function deleteHtml($result)
    {
        // We redirect back to the originator of the request
        $redirect = $this->getRequest()->getHeader('referer');
        if ($result instanceof ResponseInterface && $result->getStatusCode() == 204) {
            return $result
                ->setHeader('Location', $redirect)
                ->setStatusCode(302);
        }

        return $this->redirect($redirect);
    }

    /**
     * Return the Model name derived from the Controller
     *
     * @param mixed $controllerName The name of the controller, or the controller itself
     *
     * @return string The name of the corresponding Model.
     */
    public static function getModelName($controllerName = null)
    {
        if (is_object($controllerName)) {
            $controllerName = get_class($controllerName);
        }
        $controllerName = $controllerName ?: get_called_class();
        $reflector = new \ReflectionClass($controllerName);
        $namespace = preg_replace('/\\\\Controllers$/', '\\Models', $reflector->getNamespaceName());
        $modelName = basename(str_replace('\\', DIRECTORY_SEPARATOR, $controllerName));
        $modelName = new String(preg_replace('/Controller$/', '', $modelName));
        $modelName = $namespace . '\\' . $modelName->singularize()->camelCase();
        if ($modelName[0] !== '\\') {
            $modelName = '\\' . $modelName;
        }

        return $modelName;
    }

    /**
     * Get the Model Binding, using the name of the controller to determine the binding.
     *
     * I would love to make this static, but we need the container to get the factory,
     * to get the binding.
     *
     * @param string $modelName The name of the model to get the binding for.
     *
     * @return \Backend\Interfaces\BindingInterface
     */
    public function getBinding($modelName = null)
    {
        $modelName = $modelName ?: self::getModelName();

        return $this->container
            ->get('binding_factory')
            ->build($modelName);
    }
}
