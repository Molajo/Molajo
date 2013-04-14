<?php
/**
 * Front Controller Interface
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Foundation\Api;

defined('MOLAJO') or die;

use Exception;
use Molajo\Foundation\Exception\FrontControllerException;

/**
 * Front Controller Interface
 *
 * 1. Initialise
 * 2. Route
 * 3. Authorise
 * 4. Execute (Display or Action)
 * 5. Respond
 *
 * In addition, schedules onAfter events after each of the above.
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
interface FrontControllerInterface
{
    /**
     * Primary Logic Flow, executes each of the following and 'onAfter' events
     *
     * 1. Initialise
     * 2. Route
     * 3. Authorise
     * 4. Execute (Display or Action)
     * 5. Respond
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function process();

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function get($key = null, $default = null);

    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function set($key, $value = null);

    /**
     * Initialise Application, including invoking Dependency Injection Container and
     *  instantiating services defined in Services.xml
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function initialise();

    /**
     * Evaluates HTTP Request to determine routing requirements, including:
     *
     * - Normal page request: returns array of route parameters
     * - Issues redirect request for "home" duplicate content (i.e., http://example.com/index.php, etc.)
     * - Checks for 'Application Offline Mode', sets a 503 error and registry values for View
     * - For 'Page not found', sets 404 error and registry values for Error Template/View
     * - For defined redirect with Catalog, issues 301 Redirect to new URL
     * - For 'log in requirement' situations, issues 303 redirect to configured log in page
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function route();

    /**
     * Authorise
     *
     * Standard Permissions Verification using action/task and catalog id for logged on user
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function authorise();

    /**
     * Execute the action requested
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function execute();

    /**
     * Return HTTP response
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public function response();

    /**
     * FrontController::diContainer is accessed using diContainer::
     *
     * @param   null $class
     *
     * @static
     * @return  null|object Services
     * @since   1.0
     * @throws  FrontControllerException
     * @api
     */
    public static function diContainer($class = null);

    /**
     * Exception Handler
     *
     * @param   Exception $e
     *
     * @return  mixed
     * @since   1.0
     * @api
     */
    public function exception_handler(Exception $e);
}
