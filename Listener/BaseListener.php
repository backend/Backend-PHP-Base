<?php
/**
 * File defining \Backend\Base\Listener\BaseListener
 *
 * PHP Version 5.3
 *
 * @category   Backend
 * @package    Base
 * @subpackage Listeners
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright  2011 - 2012 Jade IT (cc)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
namespace Backend\Base\Listener;

use Backend\Interfaces\DependencyInjectionContainerInterface;
use Backend\Interfaces\ResponseInterface;
use Backend\Core\Response;
use Backend\Core\Exception as CoreException;
use \Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

/**
 * The Base Listener.
 *
 * @category   Backend
 * @package    Base
 * @subpackage Listeners
 * @author     J Jurgens du Toit <jrgns@backend-php.net>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @link       http://backend-php.net
 */
class BaseListener
{
    /**
     * The DI Container for the Listener
     *
     * @var \Backend\Interfaces\DependencyInjectionContainerInterface
     */
    private $container;

    /**
     * The object constructor.
     *
     * @param \Backend\Interfaces\DependencyInjectionContainerInterface $container
     * The DI Container.
     */
    public function __construct(DependencyInjectionContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Method to handle core.init Events.
     *
     * It starts an output buffer and checks for the session.
     *
     * @param  \Symfony\Component\EventDispatcher\Event $event The event to handle.
     * @return void
     */
    public function coreInitEvent(\Symfony\Component\EventDispatcher\Event $event)
    {
        ob_start();
        if ($this->container->has('session')) {
            // Initialize the session
            $this->container->get('session');
        }
    }

    /**
     * Method to handle core.request Events.
     *
     * Just log the request coming in.
     *
     * @param  \Backend\Core\Event\RequestEvent $event The event to handle.
     * @return void
     */
    public function coreRequestEvent(\Backend\Core\Event\RequestEvent $event)
    {
        if ($this->container->has('logger')) {
            $request = $event->getRequest();
            $message = $request->getMethod() . ' ' . $request->getPath();
            $this->container->get('logger')->info('Request: ' . $message);
        }
    }

    /**
     * Method to handle core.callback Events.
     *
     * It applies a couple of transforms on the object, ensuring consistency.
     *
     * @param  \Backend\Core\Event\CallbackEvent $event The event to handle
     * @return void
     */
    public function coreCallbackEvent(\Backend\Core\Event\CallbackEvent $event)
    {
        $callback = $event->getCallback();

        // Check Permissions
        if ($this->container->has('authenticator')) {
            $this->container->get('authenticator')->check($callback, $this->container);
        }

        // Log the Callback
        if ($this->container->has('logger')) {
            $this->container->get('logger')->info('Callback: ' . $callback);
        }

        $event->setCallback($callback);
    }

    /**
     * Method to handle core.result Events.
     *
     * It will try to get a default format if none is specified.
     *
     * @param  \Backend\Core\Event\CallbackEvent $event The event to handle
     * @return void
     */
    public function coreResultEvent(\Backend\Core\Event\ResultEvent $event)
    {
        $result = $event->getResult();
        if ($result instanceof ResponseInterface) {
            return $result;
        }

        // Get the Formatter
        if ($this->container->has('formatter') === false || $this->container->get('formatter') === null) {
            $defaultFormatter = $this->container->hasParameter('formatter.default')
                ? $this->container->getParameter('formatter.default')
                : 'backend.base.formats.html';
            if ($this->container->has($defaultFormatter) === false) {
                throw new \Backend\Core\Exception('Unsupported format requested', 415);
            }
            $this->container->set('formatter', $this->container->get($defaultFormatter));
        }

        // Allow the controller to format the result
        $result = $this->callbackFormat($result);

        // Pass the result to the formatter
        $formatter = $this->container->get('formatter');

        // Check for any open buffers
        // TODO This isn't optimal. We don't close any unclosed sessions
        if (ob_get_level() > 1 && in_array('ob_gzhandler', ob_list_handlers()) === false) {
            $buffered = ob_get_clean();
            if (is_callable(array($formatter, 'setValue'))) {
                $formatter->setValue('buffered', $buffered);
            }
        }

        $response = $formatter->transform($result);
        $event->setResponse($response);
    }

    /**
     * Helper method to run the Callback's formatting.
     *
     * @param mixed $result The result from the callback
     *
     * @return mixed The transformed result.
     */
    protected function callbackFormat($result)
    {
        // Get and Check the initial callback
        $callback = $this->container->get('callback');
        // Check the Method
        $method = $callback->getMethod();
        if (empty($method)) {
            return $result;
        }

        $formatter = $this->container->get('formatter');
        // Setup the formatting callback
        $class = explode('\\', get_class($formatter));
        $method = str_replace('Action', end($class), $method);
        $callback->setMethod($method);

        // Execute
        try {
            $result = $callback->execute(array($result));
        } catch (CoreException $e) {

            // If the callback is invalid, it won't be called, result won't change
        }
        return $result;
    }

    /**
     * Method to handle core.exception Events.
     *
     * It will log the exception and redirect in special cases.
     *
     * @param  \Backend\Core\Event\CallbackEvent $event The event to handle
     * @return void
     */
    public function coreExceptionEvent(\Backend\Core\Event\ExceptionEvent $event)
    {
        $exception = $event->getException();

        switch ($exception->getCode()) {
            case 401:
                // Log it
                if ($this->container->has('logger')) {
                    $path = $this->container->get('request')->getPath();
                    $message = 'Unauthorized Request:' . $path;
                    $this->container->get('logger')->notice($message);
                }

                // Redirect to the predefined location
                try {
                    $location = $this->container->getParameter('unauthorized.redirect');
                    if ($location[0] === '/' && (empty($location[1]) || $location[1] !== '/')) {
                        // Relative Redirect
                        $location = $this->container->get('request_context')->getLink() . $location;
                    }
                } catch (ParameterNotFoundException $e) {
                    $location = $this->container->get('request_context')->getLink();
                }

                // Build The Response
                $response = $event->getResponse();
                $response = $response ?: new Response();
                $response->setStatusCode(302);
                $response->setHeader('Location', $location);

                // Set the Response
                $event->setResponse($response);
                break;
            default:
                // Log it
                if ($this->container->has('logger')) {
                    $message = 'Unhandled Exception: ' . $exception->getMessage();
                    $this->container->get('logger')->crit($message);
                }
                break;
        }
    }

    /**
     * Method to handle core.shutdown Events.
     *
     * It checks if a fatal error has occured and logs it.
     *
     * @param  \Backend\Core\Event\CallbackEvent $event The event to handle
     * @param  array                             $e     A dummy error used for testing.
     * @return void
     */
    public function coreShutdownEvent(\Symfony\Component\EventDispatcher\Event $event, $e = null)
    {
        $e = $e ?: error_get_last();
        if ($e !== null && in_array($e['type'], array(E_ERROR, E_USER_ERROR))) {
            $message = 'Fatal Error: ' . $e['message'];
            if ($this->container->has('logger')) {
                $this->container->get('logger')->crit(
                    'Fatal Error',
                    array('error' => $e)
                );
            }
        }
    }

    /**
     * Get the DI Container.
     *
     * @return \Backend\Interfaces\DependencyInjectionContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
