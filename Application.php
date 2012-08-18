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
     * Get the Formatter for the Application.
     *
     * @return \Backend\Interfaces\FormatterInterface
     * @todo Do this with the DIC at some point
     */
    public function getFormatter()
    {
        try {
            parent::getFormatter();
        } catch (CoreException $e) {
            try {
                if ($e->getCode() === 415 && $this->container->has('backend.base.formats.html')) {
                    $this->formatter = $this->container->get('backend.base.formats.html');
                }
            } catch (\Exception $e) {
                // Can't even get the backup formatter.
            }
        }
        if (empty($this->formatter)) {
            throw new CoreException('Unsupported format requested', 415);
        }

        return $this->formatter;
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
     */
    public function exception(\Exception $exception, $return = false)
    {
        switch ($exception->getCode()) {
            case 401:
                $response = new Response('', 302);
                $location = $this->container->getParameter('user.unauthorized.redirect');
                $response->addHeader($location, 'Location');
                break;
            default:
                $response = $this->renderException($exception);
                break;
        }
        // Return or Output
        if ($return) {
            return $response;
        }
        $response->output();
        die;
    }

    public function renderException($exception)
    {
        $response = parent::exception($exception, true);
        $response->setBody($exception);
        try {
            $formatter = $this->getFormatter();
        } catch (\Exception $e) {
        }
        if (empty($formatter)) {
            return new Response((string)$exception);
        }
        return $formatter->transform($response);
    }
}
