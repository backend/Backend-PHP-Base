<?php
/**
 * File defining \Backend\Base\Listener\CoreListener
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
use Backend\Core\Response;
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
     * It starts an output buffer.
     *
     * @param  \Symfony\Component\EventDispatcher\Event $event The event to handle.
     * @return void
     */
    public function coreInitEvent(\Symfony\Component\EventDispatcher\Event $event)
    {
        if ($this->container->has('session')) {
            // Initialize the session
            $this->container->get('session');
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
     * @return void
     */
    public function coreShutdownEvent(\Symfony\Component\EventDispatcher\Event $event)
    {
        $e = error_get_last();
        if ($e !== null && $e['type'] === E_ERROR) {
            $message = 'Fatal Error: ' . $e['message'];
            if ($this->container->has('logger')) {
                $this->container->get('logger')->alert(
                    'Fatal Error', array('exception' => $e)
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