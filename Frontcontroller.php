<?php
/**
 * Frontend Controller
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Front Controller for Niambie
 *
 * 1. Initialise
 * 2. Route
 * 3. Authorise
 * 4. Execute (Display or Action)
 * 5. Respond
 *
 * In addition, schedules onAfter events after each of the above.
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
Class Frontcontroller
{
    /**
     * Frontcontroller::Services
     *
     * @static
     * @var    object  Services
     * @since  1.0
     */
    protected static $services = null;

    /**
     * Assets Service
     *
     * @var    object
     * @since  1.0
     */
    protected $asset_service = null;

    /**
     * Metadata Service
     *
     * @var    object
     * @since  1.0
     */
    protected $metadata_service = null;

    /**
     * Configuration Service
     *
     * @var    object
     * @since  1.0
     */
    protected $configuration_service = null;

    /**
     * Request Service
     *
     * @var    object
     * @since  1.0
     */
    protected $request_service = null;

    /**
     * Site Service
     *
     * @var    object
     * @since  1.0
     */
    protected $site_service = null;

    /**
     * Application Service
     *
     * @var    object
     * @since  1.0
     */
    protected $application_service = null;

    /**
     * $requested_resource_for_route
     *
     * ex. articles/article-1/index.php?tag=xyz
     *
     * @var    string
     * @since  1.0
     */
    protected $requested_resource_for_route = null;

    /**
     * $base_url_path_for_application
     *
     * ex. http://site1/admin/
     *
     * @var    string
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
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $parameter_properties_array = array(
        'application_login_requirement',
        'application_home_catalog_id',
        'application_html5',
        'application_line_end',
        'application_mimetype',
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
        'asset_service',
        'metadata_service',
        'application_service',
        'site_service',
        'configuration_service',
        'configuration_application_login_requirement',
        'configuration_application_home_catalog_id',
        'configuration_offline_switch',
        'configuration_sef_url',
        'error_code',
        'error_message',
        'error_theme_id',
        'error_page_view_id',
        'redirect_to_id',
        'language_current',
        'language_direction',
        'permission_filters',
        'permission_action_to_authorisation',
        'permission_action_to_controller',
        'permission_tasks',
        'request_action',
        'request_base_url_path',
        'request_base_url',
        'request_catalog_id',
        'request_filters',
        'request_id',
        'request_method',
        'request_mimetype',
        'request_non_route_parameters',
        'request_post_variables',
        'request_task',
        'request_task_controller',
        'request_task_permission',
        'request_task_values',
        'request_date',
        'request_url',
        'request_using_ssl',
        'base_url_path_for_application',
        'requested_resource_for_route',
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

        'Service'               => 'Molajo\\Service\\Services',
        'DatabaseService'       => 'Molajo\\Service\\Services\\Database\\',
        'DateService'           => 'Molajo\\Service\\Services\\Date\\',
        'LanguageService'       => 'Molajo\\Service\\Services\\Language\\',
        'PermissionsService'    => 'Molajo\\Service\\Services\\Permissions\\',
        'CacheService'          => 'Molajo\\Service\\Services\\Cache\\',
        'ConfigurationService'  => 'Molajo\\Service\\Services\\Configuration\\',
        'ExceptionService'      => 'Molajo\\Service\\Services\\Exception\\',
        'RegistryService'       => 'Molajo\\Service\\Services\\Registry\\',
        'RequestService'        => 'Molajo\\Service\\Services\\Request\\',
        'SiteService'           => 'Molajo\\Service\\Services\\Site\\',
        'ApplicationService'    => 'Molajo\\Service\\Services\\Application\\',
        'FilesystemService'     => 'Molajo\\Service\\Services\\Filesystem\\',
        'EventService'          => 'Molajo\\Service\\Services\\Event\\',
        'ProfilerService'       => 'Molajo\\Service\\Services\\Profiler\\',
        'AssetService'          => 'Molajo\\Service\\Services\\Asset\\',
        'AuthenticationService' => 'Molajo\\Service\\Services\\Authentication\\',
        'ClientService'         => 'Molajo\\Service\\Services\\Client\\',
        'CookieService'         => 'Molajo\\Service\\Services\\Cookie\\',
        'MetadataService'       => 'Molajo\\Service\\Services\\Metadata\\',
        'RouteService'          => 'Molajo\\Service\\Services\\Route\\',
        'UserService'           => 'Molajo\\Service\\Services\\User\\',
        'ThemeService'          => 'Molajo\\Service\\Services\\Theme\\ThemeService',
        'ContentHelper'         => 'Molajo\\Service\\Services\\Theme\\Helper\\ContentHelper',
        'ExtensionHelper'       => 'Molajo\\Service\\Services\\Theme\\Helper\\ExtensionHelper',
        'ThemeHelper'           => 'Molajo\\Service\\Services\\Theme\\Helper\\ThemeHelper',
        'ViewHelper'            => 'Molajo\\Service\\Services\\Theme\\Helper\\ViewHelper',
        'Includer'              => 'Molajo\\Service\\Services\\Theme\\Includer',
        'HeadIncluder'          => 'Molajo\\Service\\Services\\Theme\\Includer\\HeadIncluder',
        'MessageIncluder'       => 'Molajo\\Service\\Services\\Theme\\Includer\\MessageIncluder',
        'PageIncluder'          => 'Molajo\\Service\\Services\\Theme\\Includer\\PageIncluder',
        'ProfilerIncluder'      => 'Molajo\\Service\\Services\\Theme\\Includer\\ProfilerIncluder',
        'TagIncluder'           => 'Molajo\\Service\\Services\\Theme\\Includer\\TagIncluder',
        'TemplateIncluder'      => 'Molajo\\Service\\Services\\Theme\\Includer\\TemplateIncluder',
        'ThemeIncluder'         => 'Molajo\\Service\\Services\\Theme\\Includer\\ThemeIncluder',
        'WrapIncluder'          => 'Molajo\\Service\\Services\\Theme\\Includer\\WrapIncluder',
        'Controller'            => 'Molajo\\MVC\\Controller',
        'CreateController'      => 'Molajo\\MVC\\Controller\\CreateController',
        'DeleteController'      => 'Molajo\\MVC\\Controller\\DeleteController',
        'DisplayController'     => 'Molajo\\MVC\\Controller\\DisplayController',
        'LoginController'       => 'Molajo\\MVC\\Controller\\LoginController',
        'LogoutController'      => 'Molajo\\MVC\\Controller\\LogoutController',
        'UpdateController'      => 'Molajo\\MVC\\Controller\\UpdateController',
        'Model'                 => 'Molajo\\MVC\\Model\\',
        'CreateModel'           => 'Molajo\\MVC\\Model\\CreateModel',
        'DeleteModel'           => 'Molajo\\MVC\\Model\\DeleteModel',
        'LoginModel'            => 'Molajo\\MVC\\Model\\LoginModel',
        'LogoutModel'           => 'Molajo\\MVC\\Model\\LogoutModel',
        'ReadModel'             => 'Molajo\\MVC\\Model\\ReadModel'
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
     * @throws \Exception
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

        if ($override_parameter_properties_array == null) {
        } else {
            $this->parameter_properties_array = $override_parameter_properties_array;
        }

        /** 1. Initialise */
        try {
            $this->initialise($override_url_request);
            $this->scheduleEvent('onAfterInitialiseEvent');

        } catch (\Exception $e) {

            throw new \Exception('Initialise Error: ' . $e->getMessage(), $e->getCode(), $e);
        }

        /** 2. Route */
        try {
            if (defined(PROFILER_ON)) {
                Services::Profiler()->set('current_phase', 'Routing');
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
                Services::Profiler()->set('current_phase', 'Authorise');
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
     * @throws  \OutOfRangeException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException('Frontcontroller: is attempting to get value for unknown key: ' . $key);
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
     * @throws  \OutOfRangeException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException('Frontcontroller: is attempting to set value for unknown key: ' . $key);
        }

        $this->parameters[$key] = $value;

        return $this->parameters[$key];
    }

    /**
     * Initialise Site, Application, and Services
     *
     * @param   $override_url_request
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    protected function initialise($override_url_request)
    {
        /** Error and Exception Handling */
        set_exception_handler(array($this, 'exception_handler'));
        set_error_handler(array($this, 'error_handler'), E_ALL);

        /** PHP Minimum */
        $results = version_compare(PHP_VERSION, '5.3', '<');
        if ($results == 1) {
            throw new \Exception
            ('Frontcontroller: PHP version ' . PHP_VERSION . ' does not meet 5.3 minimum.');
        }

        if (defined('CONTROLLER_CLASS_NAMESPACE')) {
        } else {
            define('CONTROLLER_CLASS_NAMESPACE', $this->class_array['Controller'] . '\\Controller');
        }

        if (defined('MODEL_NAMESPACE')) {
        } else {
            define('MODEL_NAMESPACE', $this->class_array['Model']);
        }

        /** Instantiate Services Controller */
        Frontcontroller::Services($this->class_array['Service']);
        Frontcontroller::Services()->set('frontcontroller_class', $this);
        Frontcontroller::Services()->set('controller_class', CONTROLLER_CLASS_NAMESPACE);

        Frontcontroller::Services()->start('ConfigurationService', $this->class_array['ConfigurationService']);
        Frontcontroller::Services()->start('RegistryService', $this->class_array['RegistryService']);
        Frontcontroller::Services()->start('RequestService', $this->class_array['RequestService']);
        Frontcontroller::Services()->start('SiteService', $this->class_array['SiteService']);
        Frontcontroller::Services()->start('ApplicationService', $this->class_array['ApplicationService']);

        /** Start Filesystem Service */
        if ($override_url_request === null) {
        } else {
            $this->set('requested_resource_for_route', $override_url_request);
        }

        /** Application (Set Application -- Sequence needed for Installation) */
        $p1 = Services::Request()->get('path_info');
        $t2 = Services::Request()->get('query_string');

        if (trim($t2) == '') {
            $requestURI = $p1;
        } else {
            $requestURI = $p1 . '?' . $t2;
        }

        Services::Application()->set('request_uri', substr($requestURI, 1, 9999));
        Services::Application()->set(
            'applications',
            Services::Configuration()->getFile('Application', 'Applications')
        );
        Services::Application()->set(
            'base_url_path_with_scheme',
            Services::Request()->get('base_url_path_with_scheme', 'text/html')
        );

        Services::Application()->setApplication();

        /** Site Identification */
        Services::Site()->set('sites', Services::Configuration()->getFile('Site', 'Sites'));
        Services::Site()->set('site_base_url', Services::Request()->get('base_url_path'));

        Services::Site()->identifySite();

        if ($override_url_request === null) {
            $this->set('requested_resource_for_route', $override_url_request);
        } else {
            $this->set(
                'requested_resource_for_route',
                Services::Application()->get('requested_resource_for_route')
            );
        }

        Services::Site()->set('custom_defines', Services::Configuration()->getFile('Application', 'Defines'));
        Services::Site()->setCustomDefines();

        $this->set('base_url_path_for_application', Services::Application()->get('base_url_path_for_application'));

        /** Add Site URL to Application */
        Services::Application()->setBaseUrlPath();

        Frontcontroller::Services()->start('CacheService', $this->class_array['CacheService']);
        Frontcontroller::Services()->start('ProfilerService', $this->class_array['ProfilerService']);
        Frontcontroller::Services()->start('FilesystemService', $this->class_array['FilesystemService']);
        Frontcontroller::Services()->start('DatabaseService', $this->class_array['DatabaseService']);
        Frontcontroller::Services()->start('EventService', $this->class_array['EventService']);
        Frontcontroller::Services()->start('DateService', $this->class_array['DateService']);
        Frontcontroller::Services()->start('PermissionsService', $this->class_array['PermissionsService']);
        Frontcontroller::Services()->start('UserService', $this->class_array['UserService']);
        $this->set('request_date', Services::Date()->getDate());

        Frontcontroller::Services()->start('LanguageService', $this->class_array['LanguageService']);

        $this->set(
            'language_current',
            Services::Registry()->get('Languages', 'Default')
        );
        $this->set(
            'language_direction',
            Services::Registry()->get('Languages' . $this->get('Language_current'))
        );

        Services::Application()->getApplication();

        $this->set('application_html5', Services::Application()->get('application_html5', 1));

        if ($this->get('application_html5') == 1) {
            $this->set('application_line_end', ('>' . chr(10)));
        } else {
            $this->set('application_line_end', ('/>' . chr(10)));
        }

        Services::Application()->setApplicationSitePaths();
        Frontcontroller::Services()->start('AssetService', $this->class_array['AssetService']);
        Frontcontroller::Services()->start('MetadataService', $this->class_array['MetadataService']);

        /** Error Theme and View */
        $this->set('error_theme_id', Services::Application()->get('error_theme_id'));
        $this->set('error_page_view_id', Services::Application()->get('error_page_view_id'));

        /** Redirects if SSL is required */
        Services::Application()->set(
            'url_force_ssl',
            (int)Services::Application()->get('url_force_ssl', 0)
        );

        Services::Application()->set('request_using_ssl', $this->get('request_using_ssl'));

        Services::Application()->sslCheck();

        $this->verifySiteApplication();

        //
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
        $class   = $this->class_array['ExceptionService'] . 'ExceptionService';
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
        $this->set(
            'configuration_application_login_requirement',
            (int)Services::Application()->get('application_login_requirement')
        );
        $this->set(
            'configuration_application_home_catalog_id',
            (int)Services::Application()->get('application_home_catalog_id')
        );
        $this->set(
            'configuration_offline_switch',
            (int)Services::Application()->get('offline_switch')
        );
        $this->set(
            'configuration_sef_url',
            (int)Services::Application()->get('sef_url')
        );

        $this->set(
            'permission_filters',
            Services::Registry()->get('Permissions', 'filters', array())
        );
        $this->set(
            'permission_action_to_authorisation',
            Services::Registry()->get('Permissions', 'action_to_authorisation', array())
        );
        $this->set(
            'permission_action_to_controller',
            Services::Registry()->get('Permissions', 'action_to_controller', array())
        );
        $this->set(
            'permission_tasks',
            Services::Registry()->get('Permissions', 'tasks', array())
        );

        $this->set(
            'user_authorised_for_offline_access',
            Services::Registry()->get('User', 'authorised_for_offline_access', 0)
        );
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

        $this->parameters                 = $route[0];
        $this->parameter_properties_array = $route[1];

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
        Services::Permissions()->verifyAction();
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
        if (defined(PROFILER_ON) && PROFILER_ON === true) {
            Services::Profiler()->set('message', 'Error Code: ' . $this->get('error_code'), 'Application');
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
                Services::Application()->get('offline_theme_id')
            );
            $this->set(
                'error_page_view_id',
                Services::Application()->get('offline_page_view_id')
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
            Services::Profiler()->set('message', 'Application Schedule Event onAfterExecute', 'Plugins');

            $results = Services::Event()->scheduleEvent('onAfterExecute');
            if (is_array($results)) {
                $results = true;
            }
        }

        if ($results === false) {
            Services::Profiler()->set('message', 'Execute ' . $action . ' failed', 'Application');

            throw new \Exception('Execute ' . $action . ' Failed', 500);
        }

        Services::Profiler()->set('message', 'Execute ' . $action . ' succeeded', 'Application');

        return;
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
     * @todo    provide script to create a full HTML website with pre-rendered pages using catalog query
     *
     * @return  mixed | bool or string
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

        if (Services::Application()->get('url_sef', 1) == 1) {
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
     * @return  void
     * @since   1.0
     */
    protected function onAfterExecuteEvent()
    {
        $this->scheduleEvent('onAfterExecuteEvent');

        return;
    }

    /**
     * Return HTTP response
     *
     * @return  object
     * @since   1.0
     * @throws  \Exception
     */
    protected function response()
    {
        Services::Profiler()->set('current_phase', 'Response', 'Application');

        if (Services::Redirect()->url === null
            && (int)Services::Redirect()->code == 0
        ) {

            Services::Profiler()
                ->set('Message', 'Response Code 200', 'Application');

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
                    . 'Application'
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
            ->set('Response exit ' . $results, 'Application');

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
        $this->scheduleEvent('onAfterResponseEvent');

        return;
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
            $model_registry      = Services::Registry()->get($model_registry_name);
        } else {
            $model_registry_name = '';
            $model_registry      = array();
        }

        $arguments = array(
            'model'                             => null,
            'model_registry'                    => $model_registry,
            'parameters'                        => $this->parameters,
            'parameter_properties_array'        => $this->parameter_properties_array,
            'query_results'                     => array(),
            'row'                               => null,
            'rendered_output'                   => $this->rendered_output,
            'class_array'                       => $this->class_array,
            'include_parse_sequence'            => array(),
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
        $modelPlugins = array();

        if ((int)Services::Registry()->get($model_registry_name, 'process_plugins') > 0) {

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
     * @return  boolean
     * @since   1.0
     */
    protected function verifySiteApplication()
    {
        $authorise = Services::Permissions()->verifySiteApplication();

        if ($authorise === false) {

            Services::Response()->setHeader(
                'Status',
                Services::Application()->get('error_403_message', 'Not Authorised.'),
                403
            );
        }

        return true;
    }

    /**
     * Frontcontroller::Services is accessed using Services::
     *
     * @param   null $class
     *
     * @static
     * @return  null|object  Services
     * @since   1.0
     * @throws \Exception
     */
    public static function Services($class = null)
    {
        if ($class === null) {
            $class = 'Molajo\\Service\\Services';
        }

        if (self::$services) {
        } else {
            try {
                self::$services = new $class();

            } catch (\RuntimeException $e) {
                throw new \Exception('Frontcontroller: Instantiate Service Exception: ', $e->getMessage());
            }
        }

        return self::$services;
    }
}
