<?php
/**
 * Front Controller
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Foundation;

defined('MOLAJO') or die;

use Exception;

use Molajo\Foundation\Exception\FrontControllerException;

use Molajo\Foundation\Api\FrontControllerInterface;

/**
 * Front Controller for Molajo
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
class FrontController implements FrontControllerInterface
{
    /**
     * FrontController::Services
     *
     * http://goo.gl/IekUW
     *
     * @static
     * @var    object
     * @since  1.0
     */
    protected static $services = null;

    /**
     * $server
     *
     * @var    object
     * @since  1.0
     */
    protected $server = null;

    /**
     * $client
     *
     * @var    object
     * @since  1.0
     */
    protected $client = null;

    /**
     * $request
     *
     * @var    object
     * @since  1.0
     */
    protected $request = null;

    /**
     * $response_instance
     *
     * @var    object
     * @since  1.0
     */
    protected $response_instance = null;

    /**
     * $redirect_instance
     *
     * @var    object
     * @since  1.0
     */
    protected $redirect_instance = null;

    /**
     * $cookie
     *
     * @var    object
     * @since  1.0
     */
    protected $cookie_instance = null;

    /**
     * $session
     *
     * @var    object
     * @since  1.0
     */
    protected $session_instance = null;

    /**
     * $site
     *
     * @var    object
     * @since  1.0
     */
    protected $site = null;

    /**
     * $application
     *
     * @var    object
     * @since  1.0
     */
    protected $application = null;

    /**
     * $route
     *
     * @var    object
     * @since  1.0
     */
    protected $route = null;

    /**
     * $user
     *
     * @var    object
     * @since  1.0
     */
    protected $user = null;

    /**
     * $rendered_output
     *
     * @var    string
     * @since  1.0
     */
    protected $rendered_output = null;

    /**
     * $profiler
     *
     * @var    object
     * @since  1.0
     */
    protected $profiler = null;

    /**
     * $exception_handler
     *
     * @var    object
     * @since  1.0
     */
    protected $exception_handler = null;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'server',
        'client',
        'request',
        'response_instance',
        'redirect_instance',
        'cookie_instance',
        'session_instance',
        'site',
        'application',
        'route',
        'user',
        'registry',
        'profiler',
        'controller_class',
        'exception_handler'
    );

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
     */
    public function process()
    {
        /** 1. Initialise */
        try {

            $this->initialise();

            $this->scheduleEvent('onAfterInitialiseEvent');

        } catch (Exception $e) {

            throw new FrontControllerException
                ('Initialise Error: ' . $e->getMessage(), $e->getCode(), $e);

        }

        /** 2. Route */
        try {
            if (defined('PROFILER_ON') && PROFILER_ON === true) {
                $this->profiler->set('current_phase', 'Routing');
            }

            $this->route();

            $this->scheduleEvent('onAfterRouteEvent');

            if (defined('ROUTE')) {
            } else {
                define('ROUTE', true);
            }

        } catch (Exception $e) {

            throw new FrontControllerException
                ('Route Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 3. Authorise */
        try {

            if (defined('PROFILER_ON') && PROFILER_ON === true) {
                $this->profiler->set('current_phase', 'Authorise');
            }

            if ($this->get('error_code', 0)) {

                $this->authorise();

                $this->scheduleEvent('onAfterAuthoriseEvent');

                if ($this->get('error_code', 0)) {
                } else {
                    $this->setError();
                }
            }

        } catch (Exception $e) {

            throw new FrontControllerException
                ('Permissions Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 4. Execute */
        try {

            $this->execute();

            $this->scheduleEvent('onAfterExecuteEvent');

        } catch (Exception $e) {

            throw new FrontControllerException
                ('Execute Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 5. Response */
        try {
            $this->response();

            $this->scheduleEvent('onAfterResponseEvent');

        } catch (Exception $e) {

            throw new FrontControllerException
                ('Response Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        exit(0);
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  FrontControllerException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new FrontControllerException
                ('FrontController: is attempting to get value for unknown key: ' . $key);
        }

        return $this->$key;
    }

    /**
     * Set the value of a specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {
            throw new FrontControllerException
            ('FrontController: is attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;

        return $this;
    }

    /**
     * Initialise Application, including invoking Dependency Injection Container and
     *  instantiating services defined in Services.xml
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     */
    public function initialise()
    {
        $this->set('controller_class', CONTROLLER_CLASS_NAMESPACE);

        /** Error and Exception Handling */
        set_exception_handler(array($this, 'exception_handler'));
        set_error_handler(array($this, 'error_handler'), 0);

        /** PHP Minimum */
        $results = version_compare(PHP_VERSION, PHP_MINIMUM_VERSION, '<');

        if ($results == 1) {
            throw new FrontControllerException
                ('FrontController: PHP version '
                    . PHP_VERSION . ' does not meet ' . PHP_MINIMUM_VERSION . ' minimum.');
        }

        /** Dependency Injection Container */
        self::diContainer();

        self::diContainer()->set('frontcontroller_instance', $this);

        /** Startup Services */
        $xml_string = $this->readXMLFile(__DIR__ . '/' . 'Services.xml');

        $services = simplexml_load_string($xml_string);

        foreach ($services->service as $service) {

            $name = (string) $service->attributes()->name;

            try {
                self::diContainer()->start($name);

            } catch (Exception $e) {

                throw new FrontControllerException
                ('DI StartServices: Exception rethrown for : ' . $name . ' ' . $e->getMessage());
            }
        }
/*
        $this->cache = self::diContainer()->start('Cache');
        self::diContainer()->start('Profiler');
        self::diContainer()->start('Database');
        self::diContainer()->start('Event');
        self::diContainer()->start('User');
        self::diContainer()->start('Permissions');
        self::diContainer()->start('Language');
        self::diContainer()->start('Date');

//todo decide how to get this logic into an after date routine
        $this->set('language_tag', Services::Language()->get('language'));
        $this->set('language_direction', Services::Language()->get('direction'));
        $this->set('request_date', Services::Date()->getDate());

        $this->application->getApplication();

        $this->set('application_html5', $this->application->get('application_html5', 1));

        if ($this->get('application_html5') == 1) {
            $this->set('application_line_end', ('>' . chr(10)));
        } else {
            $this->set('application_line_end', ('/>' . chr(10)));
        }

        $this->application->setApplicationSitePaths();
//todo end

        self::diContainer()->start('Asset');
        self::diContainer()->start('Metadata');

        /** Error Theme and View */
        $this->set('error_theme_id', $this->application->get('error_theme_id'));
        $this->set('error_page_view_id', $this->application->get('error_page_view_id'));

        /** Redirects if SSL is required */
        $this->application->set('url_force_ssl', (int) $this->application->get('url_force_ssl', 0));
        $this->application->set('request_using_ssl', $this->get('request_using_ssl'));

        $redirectTo = $this->application->sslCheck();

        if ($redirectTo === false) {
        } else {
            $this->redirect->set($redirectTo, 301);
        }

        $this->verifySiteApplication();

        $xml_string = $this->readXMLFile(VENDOR_MOLAJO_FOLDER . '/Foundation/Services.xml');
        $services   = simplexml_load_string($xml_string);
//todo: loop thru and call
        return;
    }

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
     */
    public function route()
    {
        $this->set(
            'configuration_application_login_requirement',
            (int) $this->application->get('application_login_requirement')
        );
        $this->set(
            'configuration_application_home_catalog_id',
            (int) $this->application->get('application_home_catalog_id')
        );
        $this->set('configuration_offline_switch', (int) $this->application->get('offline_switch'));
        $this->set('configuration_sef_url', (int) $this->application->get('sef_url'));
        $this->set('permission_filters', Services::Permissions()->get('filters', array()));
        $this->set(
            'permission_action_to_authorisation',
            Services::Permissions()->get('action_to_authorisation', array())
        );
        $this->set('permission_action_to_controller', Services::Permissions()->get('action_to_controller'));
        $this->set('permission_tasks', Services::Permissions()->get('tasks', array()));
        $this->set('user_authorised_for_offline_access', Services::User()->get('authorised_for_offline_access', 0));
        $this->set('user_guest', Services::User()->get('guest', 1));

        $route = self::diContainer()->start('Route');

        $route = $route->process(
            $this->parameters,
            $this->property_array,
            $this->requested_resource_for_route,
            $this->base_url_path_for_application
        );

        $this->parameters     = $route[0];
        $this->property_array = $route[1];

        if ($this->get('redirect_to_id') == 0
            && $this->get('error_code') == 0
        ) {
            return;
        }

        if ($this->get('error_code', 0)) {
            $this->set('error_message', '');
        } else {
            $this->setError();
        }

        if ($this->get('redirect_to_id', 0)) {
        } else {
            $this->redirect->url = Services::Url()->getRedirectURL((int) $this->get('redirect_to_id'));
        }

        return $this;
    }

    /**
     * Authorise
     *
     * Standard Permissions Verification using action/task and catalog id for logged on user
     *
     * @return  $this
     * @since   1.0
     */
    public function authorise()
    {
        $view_group_id  = $this->get('request_view_group_id');
        $request_action = $this->get('request_action');
        $catalog_id     = $this->get('catalog_id');

        $results = Services::Permissions()->verifyAction($view_group_id, $request_action, $catalog_id);

        if ($results === false) {
            Services::Error()->set(403);
            $this->set('error_code', 403);
            $this->set('error_message', '');
            $this->setError();
        }

        return $this;
    }

    /**
     * Execute the action requested
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontpageException
     */
    public function execute()
    {
        $action = $this->get('request_action');

        if (trim($action) == '') {
            $action = ACTION_READ;
        }

        $action = strtolower($action);
        if ($action == ACTION_READ || $action == ACTION_EDIT || $action == ACTION_CREATE) {
            $results = $this->display();
        } else {
            $results = $this->action();
        }

        if ($results === true) {
            $this->profiler->set('message', 'Application Schedule Event onAfterExecute', 'Plugins');

            $results = Services::Event()->scheduleEvent('onAfterExecute');
            if (is_array($results)) {
                $results = true;
            }
        }

        if ($results === false) {
            $this->profiler->set('message', 'Execute ' . $action . ' failed', 'Application');

            throw new FrontControllerException('Execute ' . $action . ' Failed', 500);
        }

        $this->profiler->set('message', 'Execute ' . $action . ' succeeded', 'Application');

        return;
    }

    /**
     * Executes read action -- unless page cache is available, in which case it is simply returned
     *
     * 1. Theme: recursively parses theme and then rendered output for <include:type statements
     *
     * 2. Theme Includer: each include statement is processed by the associated extension includer
     *      which retrieves data needed by the Mvc, passing control and data into the Controller
     *
     * 3. Mvc: executes actions, invoking model processing and rendering of views
     *
     * Continues until no more <include:type statements are found in the Theme and rendered output
     *
     * @return  $this
     * @since   1.0
     */
    protected function display()
    {
        $results = $this->getPageCache();

        $theme = self::diContainer()->start('Theme');

        if ($results === false) {
            $results = $theme->process(
                $this->parameters,
                $this->property_array
            );
        }

        $this->rendered_output = $results;

        $this->setPageCache();

        return $this;
    }

    /**
     * Execute action (other than Display)
     *
     * @return boolean
     * @since   1.0
     */
    protected function action()
    {

// -> sessions Services::Message()->set('Status updated', MESSAGE_TYPE_SUCCESS);

// 	What action and Controller (authorisation should be okay)
//$results = Services::Install()->content();
//$results = Services::Install()->testCreateExtension('Data Dictionary', 'Resources');
//$results = Services::Install()->testDeleteExtension('Test', 'Resources');

// what redirect for good and bad

// what parameters

        if ($this->application->get('url_sef', 1) == 1) {
            $url = $this->get('catalog_url_sef_request');

        } else {
            $url = $this->get('catalog_url_request');
        }

        $this->redirect->redirect(Services::Url()->getApplicationURL($url), '301')->send();

        return true;
    }

    /**
     * Return HTTP response
     *
     * @return  $this
     * @since   1.0
     * @throws  FrontControllerException
     */
    public function response()
    {
        $this->profiler->set('current_phase', 'Response', 'Application');

        if ($this->redirect->url === null
            && (int) $this->redirect->code == 0
        ) {

            $this->profiler->set('Message', 'Response Code 200', 'Application');

            $results = $this->response_instance->setBody($this->rendered_output)->setStatus(200)->send();

        } else {

            $this->profiler->set(
                'Response Code:' . $this->redirect->code
                    . 'Redirect to: ' . $this->redirect->url
                    . 'Application'
            );

            $results = $this->redirect->redirect()->send();
        }

        if ($results > 199 || $results < 300) {
        } else {
            throw new FrontControllerException('Response failed: ', $results);
        }

        $this->profiler->set('Response complete code: ' . $results, 'Application');

        return $this;
    }

    /**
     * Common Method for Scheduling Application Events
     *
     * @param   string $event_name
     *
     * @return  $this
     * @since   1.0
     */
    public function scheduleEvent($event_name)
    {
        if (isset($this->parameters['model_registry_name'])) {
            $model_registry_name = $this->parameters['model_registry_name'];
            $model_registry      = Services::Registry()->get($model_registry_name);
        } else {
            $model_registry_name = '';
            $model_registry      = array();
        }

        $arguments = array(
            'model'                             => null,
            'model_registry'                    => $model_registry,
            'model_registry_name'               => $model_registry_name,
            'parameters'                        => $this->parameters,
            'parameter_property_array'          => $this->property_array,
            'query_results'                     => array(),
            'row'                               => array(),
            'rendered_output'                   => $this->rendered_output,
            'view_path'                         => null,
            'view_path_url'                     => null,
            'plugins'                           => null,
            'include_parse_sequence'            => array(),
            'include_parse_exclude_until_final' => array()
        );

        $arguments = Services::Event()->scheduleEvent(
            $event_name,
            $arguments,
            $this->getPluginList($model_registry_name)
        );

        if (isset($arguments['parameters'])) {
            $this->parameters = $arguments['parameters'];
        }

        if (isset($this->parameters['model_registry_name'])) {

            $model_registry_name = $this->parameters['model_registry_name'];

            if (isset($arguments['model_registry'])) {
                Services::Registry()->delete($model_registry_name);
                Services::Registry()->createRegistry($this->get('model_registry_name'));
                Services::Registry()->loadArray($this->get('model_registry_name'), $arguments['model_registry']);
            }
        }

        if (isset($arguments['property_array'])) {
            $this->parameter_property_array = $arguments['property_array'];
        }

        if (isset($arguments['rendered_output'])) {
            $this->rendered_output = $arguments['rendered_output'];
        }

        return $this;
    }

    /**
     * Get the list of potential plugins identified with this model registry
     *
     * @param null $model_registry_name
     *
     * @return array
     * @since   1.0
     */
    protected function getPluginList($model_registry_name = null)
    {
        $modelPlugins = array();

        if ((int) Services::Registry()->get($model_registry_name, 'process_plugins') > 0) {

            $modelPlugins = Services::Registry()->get($model_registry_name, 'plugins');

            if (is_array($modelPlugins)) {
            } else {
                $modelPlugins = array();
            }
        }

        $plugins   = $modelPlugins;
        $plugins[] = 'Application';

        return $plugins;
    }

    /**
     * Verify that this site is authorised to access this application
     *
     * @return boolean
     * @since   1.0
     */
    protected function verifySiteApplication()
    {
        $authorise = Services::Permissions()->get('site_application');

        if ($authorise === false) {

            $this->response_instance->setHeader(
                'Status',
                $this->application->get('error_403_message', 'Not Authorised.'),
                403
            );
        }

        return true;
    }

    /**
     * Retrieve page from Page Cache, if cache is enabled and the page is available
     *
     * @todo    provide script to create a full HTML website with pre-rendered pages using catalog query
     *
     * @return  mixed | bool or string
     * @since   1.0
     */
    protected function getPageCache()
    {
        return $this->cache->get(PAGE_LITERAL, serialize($this->parameters));
    }

    /**
     * Set Page Cache if caching is enabled for Page
     *
     * Note: Make certain parameters only contain route values when setting cache
     *
     * @return mixed
     * @since   1.0
     */
    protected function setPageCache()
    {
        return $this->cache->set(PAGE_LITERAL, $this->rendered_output, serialize($this->parameters));
    }
    /**
     * FrontController::diContainer is accessed using diContainer::
     *
     * @param null $class
     *
     * @static
     * @return null|object Services
     * @since   1.0
     * @throws  Exception
     */
    public static function diContainer($class = null)
    {
        if ($class === null) {
            $class = 'Molajo\\DI\\Container';
        }

        if (self::$services) {
        } else {
            try {
                self::$services = new $class();

            } catch (FrontControllerException $e) {
                throw new FrontControllerException
                ('FrontController: Instantiate Dependency Injection Container class Exception: ', $e->getMessage());
            }
        }

        return self::$services;
    }

    /**
     * Read XML file and return results
     *
     * @param   $path_and_file
     *
     * @return bool|object
     * @since   1.0
     * @throws  Exception
     */
    protected function readXMLFile($path_and_file)
    {
        if (file_exists($path_and_file)) {
        } else {
            throw new FrontControllerException
            ('Configuration: readXMLFile File not found: ' . $path_and_file);
        }

        try {
            return file_get_contents($path_and_file);

        } catch (Exception $e) {

            throw new FrontControllerException
            ('Configuration: readXMLFile Failure reading File: '
                . $path_and_file . ' ' . $e->getMessage());
        }
    }

    /**
     * Exception Handler
     *
     * @param   Exception $e
     *
     * @return  void
     * @since   1.0
     */
    public function exception_handler(Exception $e)
    {
        $class   = 'Molajo\\Foundation\\Exception\\Exceptions';
        $connect = new $class($e->getMessage(), $e->getCode(), $e);
        $connect->formatMessage();

        return;
    }

    /**
     * PHP Error Handler - throw PHP Errors as PHP Exceptions
     *
     * @param string $code
     * @param string $message
     * @param string $file
     * @param string $line
     *
     * @throws  \ErrorException
     * @since   1.0
     */
    public function error_handler($code, $message, $file, $line)
    {
        if (0 == error_reporting()) {
            return;
        }
        throw new ErrorThrownAsException
        ($message, 0, $code, $file, $line);
    }

    /**
     * Establish routing information for Error
     *
     * @return void
     * @since   1.0
     */
    protected function setError()
    {
        if (defined('PROFILER_ON') && PROFILER_ON === true) {
            $this->profiler->set('message', 'Error Code: ' . $this->get('error_code'), 'Application');
        }

        $this->set('request_method', 'get');
        $this->set('request_action', 'read');
        $this->set('request_post_variables', array());
        $this->set('request_filters', array());
        $this->set('request_task_permission', 'read');
        $this->set('request_task_controller', 'read');

        if ($this->get('error_code') == 403) {
            $this->set(
                'error_message',
                Services::Registry()->get(
                    'Configuration',
                    'error_403_message',
                    Services::Language()->translate('Not Authorised')
                )
            );
        }

        if ($this->get('error_code') == 404) {
            $this->set(
                'error_message',
                Services::Registry()->get(
                    'Configuration',
                    'error_404_message',
                    Services::Language()->translate('Page not found')
                )
            );
        }

        if ($this->get('error_code') == 500) {
            if ($this->get('error_message') == '') {
                $this->set('error_message', Services::Language()->translate('Internal Server Error'));
            }
        }

        if ($this->get('error_code') == 503) {
            $this->set(
                'error_theme_id',
                $this->application->get('offline_theme_id')
            );
            $this->set(
                'error_page_view_id',
                $this->application->get('offline_page_view_id')
            );
            $this->set(
                'error_message',
                Services::Registry()->get(
                    'Configuration',
                    'offline_message',
                    Services::Language()->translate
                        (
                            'This site is not available.<br /> Please check back again soon.'
                        )
                )
            );
        }

        $this->response_instance->setStatusCode($this->get('error_code'));

        Services::Message()->set($this->get('error_message'), MESSAGE_TYPE_ERROR, $this->get('error_code'));

        return;
    }
}
