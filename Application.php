<?php
/**
 * File defining \Backend\Base\Application
 *
 * PHP Version 5.3
 *
 * @category  Backend
 * @package   Base
 * @author    J Jurgens du Toit <jrgns@backend-php.net>
 * @copyright 2011 - 2012 Jade IT (cc)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 * @link      http://backend-php.net
 */
namespace Backend\Base;
use Backend\Core\Application as CoreApplication;
use Backend\Core\Exception as CoreException;
use Backend\Interfaces\CallbackInterface;
use Backend\Interfaces\FormatterInterface;
use Backend\Core\Response;
/**
 * The main application class.
 *
 * @category Backend
 * @package  Base
 * @author   J Jurgens du Toit <jrgns@backend-php.net>
 * @license  http://www.opensource.org/licenses/mit-license.php MIT License
 * @link     http://backend-php.net
 */
class Application extends CoreApplication
{
    /**
     * Initialize the Application.
     *
     * @return boolean Returns true if the initialization ran. False otherwise.
     * @todo Use the event infrastructure for this.
     */
    public function init()
    {
        if (parent::init() === false) {
            return false;
        }
        if ($this->container->has('session')) {
            $this->container->get('session');
        }

        return true;
    }

    /**
     * Execute the defined callback.
     *
     * The callback will be logged.
     *
     * @param Backend\Interfaces\CallbackInterface $callback The callback to execute.
     *
     * @return mixed The result from the callback.
     * @todo Use the event infrastructure for this.
     */
    public function transformCallback(CallbackInterface $callback)
    {
        $callback = parent::transformCallback($callback);

        // Check Permissions
        if ($this->container->has('authenticator')) {
            $this->container->get('authenticator')->check($callback, $this->container);
        }

        // Log the Callback
        if ($this->container->has('logger')) {
            $this->container->get('logger')->info('Callback: ' . $callback);
        }

        return $callback;
    }

    /**
     * Transform the callback in relation with the format.
     *
     * @param Backend\Interfaces\CallbackInterface $callback The callback on which
     * the call will be based.
     * @param Backend\Interfaces\FormatterInterface $formatter The formatter on which
     * the call will be based.
     *
     * @return Backend\Interfaces\CallbackInterface The transformed format callback.
     * @todo Use the event infrastructure for this.
     */
    public function transformFormatCallback(CallbackInterface $callback, FormatterInterface $formatter)
    {
        $callback = parent::transformFormatCallback($callback, $formatter);

        // Log the Callback
        if ($this->container->has('logger')) {
            $this->container->get('logger')->info('Format Callback: ' . $callback);
        }

        return $callback;
    }

    /**
     * Exception handling function called when ever an exception isn't handled.
     *
     * Called by set_exception_handler. It will try to transform the exception
     * into the expected format.
     *
     * @param \Exception $exception The thrown exception.
     * @param bool       $return    Return the response instead of outputting it.
     *
     * @return \Backend\Interfaces\ResponseInterface
     * @todo Use the event infrastructure for this.
     */
    public function exception(\Exception $exception, $return = false)
    {
        switch ($exception->getCode()) {
            case 401:
                // Log it
                if ($this->container->has('logger')) {
                    $message = 'Unauthorized Request:' . $this->getRequest()->getPath();
                    $this->container->get('logger')->notice($message);
                }
                // Redirect to the predefined location
                $response = new Response('', 302);
                $location = $this->container->getParameter('unauthorized.redirect');
                if ($location[0] === '/' && (empty($location[1]) || $location[1] !== '/')) {
                    // Relative Redirect
                    $location = $this->container->get('request_context')->getLink() . $location;
                }
                $response->setHeader('Location', $location);
                break;
            default:
                // Log it
                if ($this->container->has('logger')) {
                    $message = 'Unhandled Exception: ' . $exception->getMessage();
                    $this->container->get('logger')->crit($message);
                }
                break;
        }
        // Return or Output
        if ($return) {
            return $response;
        }
        $response->output();
        die;
    }

    /**
     * Shutdown function called when ever the script ends
     *
     * @return null
     * @todo Use the event infrastructure for this.
     */
    public function shutdown()
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
        parent::shutdown();
    }
}
