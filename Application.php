<?php
/**
 * Application Frontend Controller
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
 */
namespace Molajo;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Front Controller for the Niambie Application
 *
 * 1. Initialise
 * 2. Route
 * 3. Authorise
 * 4. Execute (Display or Action)
 * 5. Respond
 *
 * In addition the Application Frontend Controller schedules onAfter events for each of the above.
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @since      1.0
 */
Class Application
{
    /**
     * Application::Services
     *
     * @static
     * @var    object  Services
     * @since  1.0
     */
    protected static $services = null;

    /**
     * Assets Registry
     *
     * @var    object
     * @since  1.0
     */
    protected $assets = null;

    /**
     * RequestService
     *
     * @var    object
     * @since  1.0
     */
    protected $request = null;

    /**
     * $requested_resource_for_route
     *
     * ex. articles/article-1/index.php?tag=xyz
     *
     * @var    object  Request
     * @since  1.0
     */
    protected $requested_resource_for_route = null;

    /**
     * $base_url_path_for_application
     *
     * ex. http://site1/admin/
     *
     * @var    object  Request
     * @since  1.0
     */
    protected $base_url_path_for_application = null;

    /**
     * $rendered_output
     *
     * @var    object
     * @since  1.0
     */
    protected $rendered_output = null;

    /**
     * $exception_handler
     *
     * @var    object
     * @since  1.0
     */
    protected $exception_handler = null;

    /**
     * Stores an array of key/value Parameters settings from Route
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * List of Properties from Route
     *
     * @var    object
     * @since  1.0
     */
    protected $parameter_properties_array = array(
        'application_login_requirement',
        'application_home_catalog_id',

        'catalog_alias',
        'catalog_category_id',
        'catalog_extension_instance_id',
        'catalog_home',
        'catalog_id',
        'catalog_model_name',
        'catalog_model_type',
        'catalog_model_registry_name',
        'catalog_page_type',
        'catalog_source_id',
        'catalog_type',
        'catalog_type_id',
        'catalog_url_request',
        'catalog_url_sef_request',
        'catalog_view_group_id',

        'configuration_application_login_requirement',
        'configuration_application_home_catalog_id',
        'configuration_offline_switch',
        'configuration_sef_url',

        'error_code',
        'error_message',
        'error_theme_id',
        'error_page_view_id',
        'redirect_to_id',

        'permission_filters',
        'permission_action_to_authorisation',
        'permission_action_to_controller',
        'permission_tasks',

        'request_action',
        'request_base_url_path',
        'request_catalog_id',
        'request_filters',
        'request_id',
        'request_method',
        'request_non_route_parameters',
        'request_post_variables',
        'request_task',
        'request_task_controller',
        'request_task_permission',
        'request_task_values',
        'request_url',

        'status_authorised',
        'status_found',

        'user_authorised_for_offline_access',
        'user_guest'
    );

    /**
     * List of Classes and Namespaces passed through application
     *
     * Use setClass in base classes to Override Class
     *
     * @var    array
     * @since  1.0
     */
    protected $class_array = array(

        'Service' => 'Molajo\\Service\\Services',
        'AssetService' => 'Molajo\\Service\\Services\\Asset\\AssetService',
        'ConfigurationService' => 'Molajo\\Service\\Services\\Configuration\\ConfigurationService',
        'ExceptionService' => 'Molajo\\Service\\Services\\Exception\\ExceptionService',
        'MetadataService' => 'Molajo\\Service\\Services\\Metadata\\MetadataService',
        'RequestService' => 'Molajo\\Service\\Services\\Request\\RequestService',
        'RouteService' => 'Molajo\\Service\\Services\\Route\\RouteService',
        'ThemeService' => 'Molajo\\Service\\Services\\Theme\\ThemeService',

        'ContentHelper' => 'Molajo\\Service\\Services\\Theme\\Helper\\ContentHelper',
        'ExtensionHelper' => 'Molajo\\Service\\Services\\Theme\\Helper\\ExtensionHelper',
        'ThemeHelper' => 'Molajo\\Service\\Services\\Theme\\Helper\\ThemeHelper',
        'ViewHelper' => 'Molajo\\Service\\Services\\Theme\\Helper\\ViewHelper',

        'Includer' => 'Molajo\\Service\\Services\\Theme\\Includer',
        'HeadIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\HeadIncluder',
        'MessageIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\MessageIncluder',
        'PageIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\PageIncluder',
        'ProfilerIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\ProfilerIncluder',
        'TagIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\TagIncluder',
        'TemplateIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\TemplateIncluder',
        'ThemeIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\ThemeIncluder',
        'WrapIncluder' => 'Molajo\\Service\\Services\\Theme\\Includer\\WrapIncluder',

        'Controller' => 'Molajo\\MVC\\Controller\\Controller',
        'CreateController' => 'Molajo\\MVC\\Controller\\CreateController',
        'DeleteController' => 'Molajo\\MVC\\Controller\\DeleteController',
        'DisplayController' => 'Molajo\\MVC\\Controller\\DisplayController',
        'LoginController' => 'Molajo\\MVC\\Controller\\LoginController',
        'LogoutController' => 'Molajo\\MVC\\Controller\\LogoutController',
        'UpdateController' => 'Molajo\\MVC\\Controller\\UpdateController',

        'Model' => 'Molajo\\MVC\\Model\\Model',
        'CreateModel' => 'Molajo\\MVC\\Model\\CreateModel',
        'DeleteModel' => 'Molajo\\MVC\\Model\\DeleteModel',
        'LoginModel' => 'Molajo\\MVC\\Model\\LoginModel',
        'LogoutModel' => 'Molajo\\MVC\\Model\\LogoutModel',
        'ReadModel' => 'Molajo\\MVC\\Model\\ReadModel'
    );

    /**
     * Override normal processing with these parameters
     *
     * @param   string  $override_url_request
     * @param   string  $override_catalog_id
     * @param   string  $override_parse_sequence
     * @param   string  $override_parse_final
     * @param   string  $override_parameter_properties_array
     * @param   string  $override_class_array
     *
     * @return  mixed
     * @since   1.0
     */
    public function process(
        $override_url_request = null,
        $override_catalog_id = null,
        $override_parse_sequence = null,
        $override_parse_final = null,
        $override_parameter_properties_array = null,
        $override_class_array = null
    ) {

        if ($override_class_array == null) {
        } else {
            $this->class_array = $override_class_array;
        }

        $class = $this->class_array['RequestService'];
        $this->request = new $class();

        $this->setBaseURL();

        $this->setDefines();

        if ($override_parameter_properties_array == null) {
        } else {
            $this->parameter_properties_array = $override_parameter_properties_array;
        }

        if ($override_url_request === null) {
        } else {
            $this->requested_resource_for_route = $override_url_request;
        }

        /** 1. Initialise */
        try {
            $this->initialise();
            $this->scheduleEvent('onAfterInitialiseEvent');

        } catch (\Exception $e) {

            throw new \Exception('Initialise Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 2. Route */
        try {
            if (defined(PROFILER_ON)) {
                Services::Profiler()->set(ROUTING, PROFILER_APPLICATION);
            }

            $this->route($override_catalog_id);
            $this->scheduleEvent('onAfterRouteEvent');

            if (defined('ROUTE')) {
            } else {
                define('ROUTE', true);
            }

        } catch (\Exception $e) {

            throw new \Exception('Route Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 3. Authorise */
        try {

            if (defined(PROFILER_ON)) {
                Services::Profiler()->set(ROUTING, PROFILER_APPLICATION);
            }

            if ($this->get('error_code', 0)) {
                $this->authorise();
                $this->scheduleEvent('onAfterAuthoriseEvent');

                if ($this->get('error_code', 0)) {
                } else {
                    $this->setError();
                }
            }

        } catch (\Exception $e) {
            throw new \Exception('Permissions Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 4. Execute */
        try {
            $this->execute($override_parse_sequence, $override_parse_final);
            $this->scheduleEvent('onAfterExecuteEvent');

        } catch (\Exception $e) {
            throw new \Exception('Execute Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 5. Response */
        try {
            $this->response();
            $this->scheduleEvent('onAfterResponseEvent');

        } catch (\Exception $e) {
            throw new \Exception('Response Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        exit(0);
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    protected function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException('Application: is attempting to get value for unknown key: ' . $key);
        }

        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        $this->parameters[$key] = $default;
        return $this->parameters[$key];
    }

    /**
     * Set the value of a specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    protected function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException('Application: is attempting to set value for unknown key: ' . $key);
        }

        $this->parameters[$key] = $value;
        return $this->parameters[$key];
    }

    /**
     * Initialise Site, Application, and Services
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    protected function initialise()
    {
        set_exception_handler(array($this, 'exception_handler'));
        set_error_handler(array($this, 'error_handler'), E_ALL);

        $results = version_compare(PHP_VERSION, '5.3', '<');
        if ($results == 1) {
            throw new \Exception('Application: PHP version ' . PHP_VERSION . ' does not meet 5.3 minimum.');
        }

        $this->setSite();

        $this->setApplication();

        $this->installCheck();

        Application::Services($this->class_array['Service'])->initiate();

        $class = $this->class_array['AssetService'];
        $this->assets = new $class();
        $this->assets->initialise();

        $currentLanguage = Services::Registry()->get(LANGUAGES_LITERAL, 'Default');
        $this->assets->set('direction',
            Services::Registry()->get(LANGUAGES_LITERAL . $currentLanguage, 'direction')
        );

        Services::Registry()->createRegistry(METADATA_LITERAL);

        $this->set('error_theme_id', Services::Registry()->get(CONFIGURATION_LITERAL, 'error_theme_id'));
        $this->set('error_page_view_id', Services::Registry()->get(CONFIGURATION_LITERAL, 'error_page_view_id'));

        $this->sslCheck();

        $this->verifySiteApplication();

        /** LAZY LOAD Session */
        //Services::Session()->create(
        //    Services::Session()->getHash(get_class($this))
        //);

        return;
    }

    /**
     * Exception Handler
     *
     * @param   \Exception $e
     *
     * @return  void
     * @since   1.0
     */
    public function exception_handler(\Exception $e)
    {
        $class = $this->class_array['ExceptionService'];
        $connect = new $class($e->getMessage(), $e->getCode(), $e);
        $connect->formatMessage();

        return;
    }

    /**
     * PHP Error Handler - throw PHP Errors as PHP Exceptions
     *
     * @param   string  $code
     * @param   string  $message
     * @param   string  $file
     * @param   string  $line
     *
     * @throws  \ErrorException
     * @since   1.0
     */
    public function error_handler($code, $message, $file, $line)
    {
        if (0 == error_reporting()) {
            return;
        }
        throw new \ErrorException($message, 0, $code, $file, $line);
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
     * @param   string  $override_catalog_id
     *
     * @return  void
     * @since   1.0
     */
    protected function route($override_catalog_id = null)
    {
        $this->set('configuration_application_login_requirement',
            (int) Services::Registry()->get(CONFIGURATION_LITERAL, 'application_login_requirement'));
        $this->set('configuration_application_home_catalog_id',
            (int) Services::Registry()->get(CONFIGURATION_LITERAL, 'application_home_catalog_id'));
        $this->set('configuration_offline_switch',
            (int) Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_switch'));
        $this->set('configuration_sef_url',
            (int) Services::Registry()->get(CONFIGURATION_LITERAL, 'sef_url'));

        $this->set('permission_filters',
            Services::Registry()->get(PERMISSIONS_LITERAL, 'filters', array()));
        $this->set('permission_action_to_authorisation',
            Services::Registry()->get(PERMISSIONS_LITERAL, 'action_to_authorisation', array()));
        $this->set('permission_action_to_controller',
            Services::Registry()->get(PERMISSIONS_LITERAL, 'action_to_controller', array()));
        $this->set('permission_tasks',
            Services::Registry()->get(PERMISSIONS_LITERAL, 'tasks', array()));

        $this->set('request_id', (int) Services::Request()->get('id', 0));
        $this->set('request_method', Services::Request()->get('method', 'GET'));
        $this->set('request_post_variables', Services::Request()->get('post_variables', array()));

        $this->set('user_authorised_for_offline_access',
            Services::Registry()->get('User', 'authorised_for_offline_access', 0));
        $this->set('user_guest', Services::Registry()->get('User', 'guest', 1));

        $class = $this->class_array['RouteService'];
        $route = new $class();

        $route = $route->process(
            $this->parameters,
            $this->parameter_properties_array,
            $this->requested_resource_for_route,
            $this->base_url_path_for_application,
            $override_catalog_id
        );

        $this->parameters = $route[0];
        $this->parameter_properties_array = $route[1];

        if ($this->get('redirect_to_id') == 0
            && $this->get('error_code') == 0) {
            return;
        }

        if ($this->get('error_code', 0)) {
            $this->set('error_message', '');
        } else {
            $this->setError();
        }

        if ($this->get('redirect_to_id', 0)) {
        } else {
            Services::Redirect()->url = Services::Url()->getRedirectURL((int)$this->get('redirect_to_id'));
        }

        return;
    }

    /**
     * Authorise
     *
     * Standard Permissions Verification using action/task and catalog id for logged on user
     *
     * @return  void
     * @since   1.0
     */
    protected function authorise()
    {
        $permissions = Services::Permissions()->verifyAction();
        //@todo verify 403

        if ($this->get('error_code', 0)) {
            $this->set('error_message', '');
        } else {
            $this->setError();
        }

        return;
    }

    /**
     * Establish routing information for Error
     *
     * @return  void
     * @since   1.0
     */
    protected function setError()
    {
        if (defined(PROFILER_ON)) {
            Services::Profiler()->set('Error Code: ' . $this->get('error_code'), PROFILER_APPLICATION);
        }

        $this->set('request_method', 'get');
        $this->set('request_action', 'read');
        $this->set('request_post_variables', array());
        $this->set('request_filters', array());
        $this->set('request_task_permission', 'read');
        $this->set('request_task_controller', 'read');

        if ($this->get('error_code') == 403) {
            $this->set('error_message',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'error_403_message',
                    Services::Language()->translate('Not Authorised')
                )
            );
        }

        if ($this->get('error_code') == 404) {
            $this->set('error_message',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'error_404_message',
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
            $this->set('error_theme_id',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_theme_id')
            );
            $this->set('error_page_view_id',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_page_view_id')
            );
            $this->set('error_message',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'offline_message',
                    Services::Language()->translate
                    ('This site is not available.<br /> Please check back again soon.')
                )
            );
        }

        Services::Response()->setStatusCode($this->get('error_code'));

        Services::Message()->set($this->get('error_message'), MESSAGE_TYPE_ERROR, $this->get('error_code'));

        return;
    }

    /**
     * Execute the action requested
     *
     * @param   string  $override_parse_sequence
     * @param   string  $override_parse_final
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    protected function execute($override_parse_sequence, $override_parse_final)
    {
        $action = $this->get('request_action');

        if (trim($action) == '') {
            $action = ACTION_READ;
        }

        $action = strtolower($action);
        if ($action == ACTION_READ || $action == ACTION_EDIT || $action == ACTION_CREATE) {
            $results = $this->display($override_parse_sequence, $override_parse_final);
        } else {
            $results = $this->action();
        }

        if ($results === true) {
            Services::Profiler()->set('Application Schedule Event onAfterExecute', PROFILER_PLUGINS);

            $results = Services::Event()->scheduleEvent('onAfterExecute');
            if (is_array($results)) {
                $results = true;
            }
        }

        if ($results === false) {
            Services::Profiler()->set('Execute ' . $action . ' failed', PROFILER_APPLICATION);
            throw new \Exception('Execute ' . $action . ' Failed', 500);
            return false;
        }

        Services::Profiler()->set('Execute ' . $action . ' succeeded', PROFILER_APPLICATION);

        return true;
    }

    /**
     * Executes read action -- unless page cache is available, in which case it is simply returned
     *
     * 1. Theme: recursively parses theme and then rendered output for <include:type statements
     *
     * 2. Theme Includer: each include statement is processed by the associated extension includer
     *      which retrieves data needed by the MVC, passing control and data into the Controller
     *
     * 3. MVC: executes actions, invoking model processing and rendering of views
     *
     * Continues until no more <include:type statements are found in the Theme and rendered output
     *
     * @param   string  $override_parse_sequence
     * @param   string  $override_parse_final
     *
     * @since   1.0
     * @return  Application
     */
    protected function display($override_parse_sequence, $override_parse_final)
    {
        $results = $this->getPageCache();

        $class = $this->class_array['ThemeService'];
        $theme = new $class();

        if ($results === false) {
            $results = $theme->process(
                $this->parameters,
                $this->parameter_properties_array,
                $this->class_array,
                $override_parse_sequence,
                $override_parse_final
            );
        }

        $this->rendered_output = $results;

        $this->setPageCache();

        return true;
    }

    /**
     * Retrieve page from Page Cache, if cache is enabled and the page is available
     *
     * @todo provide script to create a full HTML website with pre-rendered pages using catalog query
     *
     * @return  mixed | false or string
     * @since   1.0
     */
    protected function getPageCache()
    {
        return Services::Cache()->get(PAGE_LITERAL, serialize($this->parameters));
    }

    /**
     * Set Page Cache if caching is enabled for Page
     *
     * Note: Make certain parameters only contain route values when setting cache
     *
     * @return  mixed
     * @since   1.0
     */
    protected function setPageCache()
    {
        return Services::Cache()->set(PAGE_LITERAL, $this->rendered_output, serialize($this->parameters));
    }

    /**
     * Execute action (other than Display)
     *
     * @return  boolean
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

        if (Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef', 1) == 1) {
            $url = $this->get('catalog_url_sef_request');

        } else {
            $url = $this->get('catalog_url_request');
        }

        Services::Redirect()->redirect(Services::Url()->getApplicationURL($url), '301')->send();

        return true;
    }

    /**
     * Schedule Event onAfterExecute
     *
     * @return  boolean
     * @since   1.0
     */
    protected function onAfterExecuteEvent()
    {
        return $this->scheduleEvent('onAfterExecuteEvent');
    }

    /**
     * Return HTTP response
     *
     * @return  object
     * @since   1.0
     */
    protected function response()
    {
        Services::Profiler()->set(RESPONSE, PROFILER_APPLICATION);

        if (Services::Redirect()->url === null
            && (int)Services::Redirect()->code == 0
        ) {

            Services::Profiler()
                ->set('Response Code 200', PROFILER_APPLICATION);

            Services::Response()
                ->setContent($this->rendered_output)
                ->setStatusCode(200)
                ->send();

            $results = Services::Response()
                ->getStatusCode();

        } else {

            Services::Profiler()
                ->set(
                'Response Code:' . Services::Redirect()->code
                    . 'Redirect to: ' . Services::Redirect()->url
                    . PROFILER_APPLICATION
            );

            Services::Redirect()
                ->redirect()
                ->send();

            $results = Services::Redirect()->code;
        }

        if ($results == 200) {
        } else {
            throw new \Exception('Response failed', $results);
        }

        Services::Language()->logUntranslatedStrings();

        Services::Profiler()
            ->set('Response exit ' . $results, PROFILER_APPLICATION);

        return true;
    }

    /**
     * Schedule Event onAfterParseEvent Event
     *
     * Event runs after the entire document has been rendered. The rendered content is available
     * to event plugins.
     *
     * @return  void
     * @since   1.0
     */
    protected function onAfterResponseEvent()
    {
        return $this->scheduleEvent('onAfterResponseEvent');
    }

    /**
     * Common Method for Scheduling Application Events
     *
     * @param   string  $event_name
     *
     * @return  void
     * @since   1.0
     */
    protected function scheduleEvent($event_name)
    {
        if (isset($this->parameters['model_registry_name'])) {
            $model_registry_name = $this->parameters['model_registry_name'];
            $model_registry = Services::Registry()->get($model_registry_name);
        } else {
            $model_registry_name = '';
            $model_registry = array();
        }

        $arguments = array(
            'model' => null,
            'model_registry' => $model_registry,
            'parameters' => $this->parameters,
            'parameter_properties_array' => $this->parameter_properties_array,
            'query_results' => array(),
            'row' => null,
            'rendered_output' => $this->rendered_output,
            'class_array' => $this->class_array,
            'include_parse_sequence' => array(),
            'include_parse_exclude_until_final' => array()
        );

        $arguments = Services::Event()->scheduleEvent(
            $event_name,
            $arguments,
            $this->getPluginList($model_registry_name)
        );

        if (isset($arguments['class_array'])) {
            $this->parameters = $arguments['class_array'];
        }

        if (isset($arguments['parameters'])) {
            $this->parameters = $arguments['parameters'];
        }

        if (isset($arguments['parameter_properties_array'])) {
            $this->parameter_properties_array = $arguments['parameter_properties_array'];
        }

        if (isset($this->parameters['model_registry_name'])) {

            $model_registry_name = $this->parameters['model_registry_name'];

            if (isset($arguments['model_registry'])) {
                Services::Registry()->delete($model_registry_name);
                Services::Registry()->createRegistry($this->get('model_registry_name'));
                Services::Registry()->loadArray($this->get('model_registry_name'), $arguments['model_registry']);
            }
        }

        if (isset($arguments['rendered_output'])) {
            $this->rendered_output = $arguments['rendered_output'];
        }

        return;
    }

    /**
     * Get the list of potential plugins identified with this model registry
     *
     * @param   null  $model_registry_name
     *
     * @return  array
     * @since   1.0
     */
    protected function getPluginList($model_registry_name = null)
    {
        $plugins = array();

        if ((int) Services::Registry()->get($model_registry_name, 'process_plugins') > 0) {

            $modelPlugins = Services::Registry()->get($model_registry_name, 'plugins');

            if (is_array($modelPlugins)) {
            } else {
                $modelPlugins = array();
            }
        }

        $plugins[] = 'Application';

        return $plugins;
    }

    /**
     * Populate BASE_URL using scheme, host, and base URL
     *
     * Note: The Application::Request object is used instead of the Application::Request due to where
     * processing is at this point
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setBaseURL()
    {
        if (defined('BASE_URL')) {
        } else {
            /**
             * BASE_URL - root of the website with a trailing slash
             */
            define('BASE_URL', $this->request->get('base_url') . '/');
        }

        return true;
    }

    /**
     * Folders and subfolders can be relocated outside of the Apache htdocs for increased security.
     * To do so, create a defines file and override the Autoload.php file for the new namespaces.
     *
     * Note: SITES contains content that must be accessible by the Website and thus cannot be moved.
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setDefines()
    {
        if (file_exists(BASE_FOLDER . '/defines.php')) {
            include_once BASE_FOLDER . '/defines.php';
        }

        if (defined('EXTENSIONS')) {
        } else {
            define('EXTENSIONS', BASE_FOLDER . '/Extension');
        }

        if (defined('EXTENSIONS_MENUITEMS')) {
        } else {
            define('EXTENSIONS_MENUITEMS', EXTENSIONS . '/Menuitem');
        }
        if (defined('EXTENSIONS_RESOURCES')) {
        } else {
            define('EXTENSIONS_RESOURCES', EXTENSIONS . '/Resource');
        }
        if (defined('EXTENSIONS_THEMES')) {
        } else {
            define('EXTENSIONS_THEMES', EXTENSIONS . '/Theme');
        }
        if (defined('EXTENSIONS_VIEWS')) {
        } else {
            define('EXTENSIONS_VIEWS', EXTENSIONS . '/View');
        }

        if (defined('EXTENSIONS_URL')) {
        } else {
            define('EXTENSIONS_URL', BASE_URL . 'Extension');
        }
        if (defined('EXTENSIONS_THEMES_URL')) {
        } else {
            define('EXTENSIONS_THEMES_URL', BASE_URL . 'Extension/Theme');
        }
        if (defined('EXTENSIONS_VIEWS_URL')) {
        } else {
            define('EXTENSIONS_VIEWS_URL', BASE_URL . 'Extension/View');
        }

        if (defined('SERVICES')) {
        } else {
            define('SERVICES', PLATFORM_FOLDER . '/Service');
        }
        if (defined('CORE_THEMES')) {
        } else {
            define('CORE_THEMES', PLATFORM_FOLDER . '/Theme');
        }
        if (defined('CORE_VIEWS')) {
        } else {
            define('CORE_VIEWS', PLATFORM_FOLDER . '/MVC/View');
        }
        if (defined('CORE_LANGUAGES')) {
        } else {
            define('CORE_LANGUAGES', PLATFORM_FOLDER . '/Language');
        }

        if (defined('CORE_SYSTEM_URL')) {
        } else {
            define('CORE_SYSTEM_URL', BASE_URL . 'Vendor/Molajo/System');
        }
        if (defined('CORE_THEMES_URL')) {
        } else {
            define('CORE_THEMES_URL', BASE_URL . 'Vendor/Molajo/Theme');
        }
        if (defined('CORE_VIEWS_URL')) {
        } else {
            define('CORE_VIEWS_URL', BASE_URL . 'Vendor/Molajo/MVC/View');
        }

        if (defined('SITES')) {
        } else {
            define('SITES', BASE_FOLDER . '/Site');
        }

        /** Defines used to help ensure consistency of literal values in application */
        $class = $this->class_array['ConfigurationService'];
        $defines = $class::getFile('Application', 'Defines');
        foreach ($defines->define as $item) {
            if (defined((string)$item['name'])) {
            } else {
                $value = (string)$item['value'];
                define((string)$item['name'], $value);
            }
        }

        return true;
    }

    /**
     * Identifies the specific site and sets site paths for use in the application
     *
     * @return  null
     * @since   1.0
     */
    protected function setSite()
    {
        if (defined('SITES')) {
        } else {
            define('SITES', BASE_FOLDER . '/Site');
        }

        if (defined('SITES_MEDIA_FOLDER')) {
        } else {
            define('SITES_MEDIA_FOLDER', SITES . '/media');
        }

        if (defined('SITES_MEDIA_URL')) {
        } else {
            define('SITES_MEDIA_URL', BASE_URL . 'Site/media');
        }

        if (defined('SITES_DATA_OBJECT_FOLDER')) {
        } else {
            define('SITES_DATA_OBJECT_FOLDER', BASE_URL . 'Site/media');
        }

        $site_base_url = $this->request->get('base_url_path');

        if (defined('SITE_BASE_URL')) {
        } else {
            $class = $this->class_array['ConfigurationService'];
            $sites = $class::getFile('Site', 'Sites');

            foreach ($sites->site as $single) {
                if (strtolower((string)$single->site_base_url) == strtolower($site_base_url)) {
                    define('SITE_BASE_URL', (string)$single->site_base_url);
                    define('SITE_BASE_PATH', BASE_FOLDER . (string)$single->site_base_folder);
                    define('SITE_BASE_URL_RESOURCES', SITE_BASE_URL . (string)$single->site_base_folder);
                    define('SITE_DATA_OBJECT_FOLDER', SITE_BASE_PATH . '/' . DATA_OBJECT_LITERAL);
                    define('SITE_ID', $single->id);
                    define('SITE_NAME', $single->name);
                    break;
                }
            }
            if (defined('SITE_BASE_URL')) {
            } else {
                echo 'Fatal Error: Cannot identify site for: ' . $site_base_url;
                die;
            }
        }

        return true;
    }

    /**
     * Identify current application and page request
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setApplication()
    {
        $p1 = $this->request->get('path_info');
        $t2 = $this->request->get('query_string');

        if (trim($t2) == '') {
            $requestURI = $p1;
        } else {
            $requestURI = $p1 . '?' . $t2;
        }

        $requestURI = substr($requestURI, 1, 9999);

        /** extract first node for testing as application name  */
        if (strpos($requestURI, '/')) {
            $applicationTest = substr($requestURI, 0, strpos($requestURI, '/'));
        } else {
            $applicationTest = $requestURI;
        }

        $requested_resource_for_route = '';

        if (defined('APPLICATION')) {
            /* to override - must also define $this->request->get('requested_resource_for_route') */
        } else {

            $class = $this->class_array['ConfigurationService'];
            $apps = $class::getFile('Application', 'Applications');

            foreach ($apps->application as $app) {

                $xml_name = (string)$app->name;
                ;

                if (strtolower(trim($xml_name)) == strtolower(trim($applicationTest))) {

                    define('APPLICATION', $app->name);
                    define('APPLICATION_URL_PATH', APPLICATION . '/');
                    define('APPLICATION_ID', $app->id);

                    $requested_resource_for_route = substr(
                        $requestURI,
                        strlen(APPLICATION) + 1,
                        strlen($requestURI) - strlen(APPLICATION) + 1
                    );
                    break;
                }
            }

            if (defined('APPLICATION')) {
            } else {
                define('APPLICATION', $apps->default->name);
                define('APPLICATION_URL_PATH', '');
                define('APPLICATION_ID', $apps->default->id);

                $requested_resource_for_route = $requestURI;
            }
        }

        /*  Page Request used in Application::Request */
        if (strripos($requested_resource_for_route, '/') == (strlen($requested_resource_for_route) - 1)) {
            $requested_resource_for_route
                = substr($requested_resource_for_route, 0, strripos($requested_resource_for_route, '/'));
        }

        $this->requested_resource_for_route = $requested_resource_for_route;

        $this->base_url_path_for_application
            = $this->request->get('base_url_path_with_scheme')
            . '/'
            . APPLICATION_URL_PATH;

        return true;
    }

    /**
     * Determine if the site has already been installed
     *
     * return  boolean
     * @since  1.0
     */
    protected function installCheck()
    {
        if (defined('SKIP_INSTALL_CHECK')) {
            return true;
        }

        if (APPLICATION == 'installation') {
            return true;
        }

        if (file_exists(SITE_BASE_PATH . '/Dataobject/Database.xml')
            && filesize(SITE_BASE_PATH . '/Dataobject/Database.xml') > 10
        ) {
            return true;
        }
//@todo - install		/** Redirect to Installation Application */
        $redirect = BASE_URL . 'installation/';
        header('Location: ' . $redirect);

        exit();
    }

    /**
     * Check to see if secure access to the application is required by configuration
     *
     * @return  bool
     * @since   1.0
     */
    protected function sslCheck()
    {
        Services::Registry()->get('ApplicationsParameters');

        if ((int)Services::Registry()->get(CONFIGURATION_LITERAL, 'url_force_ssl', 0) > 0) {

            if (($this->request->get('connection')->isSecure() === true)) {

            } else {

                $redirectTo = (string)'https' .
                    substr(BASE_URL, 4, strlen(BASE_URL) - 4) .
                    APPLICATION_URL_PATH .
                    '/' . $this->request->get('requested_resource_for_route');

                Services::Redirect()
                    ->set($redirectTo, 301);

                return false;
            }
        }

        return true;
    }

    /**
     * Verify that this site is authorised to access this application
     *
     * @return  boolean
     * @since   1.0
     */
    protected function verifySiteApplication()
    {
        $authorise = Services::Permissions()->verifySiteApplication();

        if ($authorise === false) {

            Services::Response()->setHeader(
                'Status',
                Services::Registry()->get(CONFIGURATION_LITERAL, 'error_403_message', 'Not Authorised.'),
                403
            );
        }

        return true;
    }

    /**
     * Application::Services is accessed using Services::
     *
     * @param   null $class
     *
     * @static
     * @return  null|object  Services
     * @throws  \RuntimeException
     * @since   1.0
     */
    public static function Services($class = null)
    {
        if (self::$services) {
        } else {
            try {
                self::$services = new $class();

            } catch (\RuntimeException $e) {
                echo 'Instantiate Service Exception : ', $e->getMessage(), "\n";
                die;
            }
        }

        return self::$services;
    }
}
