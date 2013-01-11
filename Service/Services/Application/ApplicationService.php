<?php
/**
 * Application Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Application;

defined('NIAMBIE') or die;

/**
 * Application Services
 *
 * 1. Identifies the current Application
 * 2. Load Application Configuration
 * 3. Defines Site Paths for Application
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 *
 * Usage:
 *
 *  To retrieve Configuration data for the Application:
 *
 *  Services::Application()->get($key);
 *
 *  Services::Application()->set($key, $value);
 *
 *  System Class, not a Frontend Developer Resource
 */
Class ApplicationService
{
    /**
     * Application ID
     *
     * @var    string
     * @since  1.0
     */
    protected $application_id;

    /**
     * Application Name
     *
     * @var    string
     * @since  1.0
     */
    protected $application_name;

    /**
     * Application Base URL
     *
     * @var    string
     * @since  1.0
     */
    protected $application_base_url;

    /**
     * Application Catalog Type ID
     *
     * @var    string
     * @since  1.0
     */
    protected $application_catalog_type_id;

    /**
     * Application Description
     *
     * @var    string
     * @since  1.0
     */
    protected $application_description;

    /**
     * Application Path
     *
     * @var    string
     * @since  1.0
     */
    protected $application_path;

    /**
     * Applications XML identifying applications for this implementation
     *
     * @var    object
     * @since  1.0
     */
    protected $applications = null;

    /**
     * Base URL Path for Application
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url_path_for_application = null;

    /**
     * Base URL Path with Scheme
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url_path_with_scheme = null;

    /**
     * Calling Class
     *
     * @var    string
     * @since  1.0
     */
    protected $calling_class = false;

    /**
     * Calling Method
     *
     * @var    string
     * @since  1.0
     */
    protected $calling_method = false;

    /**
     * Application Configuration Data
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = array();

    /**
     * Request URI
     *
     * @var    string
     * @since  1.0
     */
    protected $request_uri = null;

    /**
     * Request using SSL
     *
     * @var    bool
     * @since  1.0
     */
    protected $request_using_ssl = false;

    /**
     * Resource portion of the URL for Route
     *
     * @var    string
     * @since  1.0
     */
    protected $requested_resource_for_route = null;

    /**
     * Site Base URL
     *
     * @var    string
     * @since  1.0
     */
    protected $site_base_url = null;

    /**
     * Configuration Option for Forcing SSL
     *
     * @var    string
     * @since  1.0
     */
    protected $url_force_ssl = null;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'application_base_url',
        'application_catalog_type_id',
        'application_description',
        'application_id',
        'application_name',
        'application_path',
        'applications',
        'base_url_path_for_application',
        'base_url_path_with_scheme',
        'calling_class',
        'calling_method',
        'parameters',
        'request_uri',
        'request_using_ssl',
        'requested_resource_for_route',
        'site_base_url',
        'url_force_ssl'
    );

    /**
     * Class constructor
     *
     * @since   1.0
     */
    public function __construct()
    {
        $trace = debug_backtrace();
        if (isset($trace[1])) {
            $this->set('calling_class', $trace[1]['class']);
            $this->set('calling_method', $trace[1]['function']);
        }

        return;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  \Exception
     */
    public function get($key, $default = null)
    {
        $key = strtolower($key);
        echo $key . '<br />';
        if (in_array($key, $this->property_array)) {
            echo $key . '<br />';
            if (isset($this->$key)) {
                return $this->$key;
            }

            $this->$key = $default;

            return $this->$key;
        }

        if (isset($this->parameters[$key])) {

        } else {
            $this->parameters[$key] = $default;
        }

        return $this->parameters[$key];
    }

    /**
     * Set the value of the specified key
     *
     * Parameters are set in the configuration file or by updating the entire $parameters array
     *  and passing it back in - as a full array
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  \Exception
     */
    public function set($key, $value)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
            $this->$key = $value;

            return $this->$key;
        }

        $this->parameters[$key] = $value;

        return $this->parameters[$key];
    }

    /**
     * Using Request URI, identify current application and page request
     *
     * @return  void
     * @since   1.0
     */
    public function setApplication()
    {
        if (strpos($this->request_uri, '/')) {
            $applicationTest = substr($this->request_uri, 0, strpos($this->request_uri, '/'));
        } else {
            $applicationTest = $this->request_uri;
        }

        $this->requested_resource_for_route = '';

        if (defined('APPLICATION')) {
            /* to override - must also define $this->request->get('requested_resource_for_route') */
        } else {

            foreach ($this->applications->application as $app) {

                $xml_name = (string)$app->name;

                if (strtolower(trim($xml_name)) == strtolower(trim($applicationTest))) {

                    define('APPLICATION', $app->name);
                    define('APPLICATION_URL_PATH', APPLICATION . '/');
                    define('APPLICATION_ID', $app->id);

                    $this->requested_resource_for_route = substr(
                        $this->request_uri,
                        strlen(APPLICATION) + 1,
                        strlen($this->request_uri) - strlen(APPLICATION) + 1
                    );

                    break;
                }
            }

            if (defined('APPLICATION')) {
            } else {
                define('APPLICATION', $this->applications->default->name);
            }
            if (defined('APPLICATION_URL_PATH')) {
            } else {
                define('APPLICATION_URL_PATH', '');
            }
            if (defined('APPLICATION_ID')) {
            } else {
                define('APPLICATION_ID', $this->applications->default->id);
            }

            $this->requested_resource_for_route = $this->request_uri;
        }

        /*  Page Request used in Application::Request */
        if (strripos($this->requested_resource_for_route, '/') == (strlen($this->requested_resource_for_route) - 1)) {
            $this->requested_resource_for_route
                = substr($this->requested_resource_for_route, 0, strripos($this->requested_resource_for_route, '/'));
        }

        return;
    }

    /**
     * Append Application Node to Scheme + Base URL for use creating URLs for the Application
     *
     * @return  void
     * @since   1.0
     */
    public function setBaseUrlPath()
    {
        $this->base_url_path_for_application = $this->base_url_path_with_scheme . '/' . APPLICATION_URL_PATH;

        return;
    }

    /**
     * Determine if the Application must use SSL, according to Configuration Data
     * If so, determine if SSL is already in use
     * If not, redirect using HTTPS
     *
     * @return  bool|string
     * @since   1.0
     */
    public function sslCheck()
    {
        if ((int)$this->url_force_ssl > 0) {

            if (($this->request_using_ssl === true)) {

            } else {

                return (string)'https' .
                    substr(BASE_URL, 4, strlen(BASE_URL) - 4) .
                    APPLICATION_URL_PATH .
                    '/' . $this->requested_resource_for_route;
            }
        }

        return false;
    }

    /**
     * Retrieve Application Configuration Data
     *
     * @return  void
     * @since   1.0
     * @throws  \Exception
     */
    public function getApplication()
    {
        if (APPLICATION == 'installation') {

            $this->application_id              = 0;
            $this->application_catalog_type_id = CATALOG_TYPE_APPLICATION;
            $this->application_name            = APPLICATION;
            $this->application_description     = APPLICATION;
            $this->application_path            = APPLICATION;

        } else {

            try {
                $controllerClass = CONTROLLER_CLASS_NAMESPACE;
                $controller      = new $controllerClass();
                $controller->getModelRegistry('Datasource', 'Application', 1);

                $controller->set('name_key_value', APPLICATION, 'model_registry');

                $item = $controller->getData(QUERY_OBJECT_ITEM);

                if ($item === false) {
                    throw new \Exception ('ConfigurationService: Error executing getApplication Query');
                }

                $this->parameters = array();

                $this->application_id              = (int)$item->id;
                $this->application_catalog_type_id = (int)$item->catalog_type_id;
                $this->application_name            = $item->name;
                $this->application_path            = $item->path;
                $this->application_description     = $item->description;

                foreach ($item as $key => $value) {

                    if ($key == 'parameters' || $key == 'metadata') {
                    } elseif (substr($key, 0, strlen('parameters_')) == strtolower('parameters_')) {
                        $key                    = substr(
                            $key,
                            strlen('parameters_'),
                            strlen($key) - strlen('parameters_')
                        );
                        $this->parameters[$key] = $value;
                    } else {
                        $this->parameters[$key] = $value;
                    }
                }

            } catch (\Exception $e) {
                throw new \Exception('Configuration: Exception caught in Configuration: ' . $e->getMessage());
            }
        }

        return;
    }

    /**
     * Establish Site paths for media, cache, log, etc., locations as configured for this Application
     *
     * @return  void
     * @since   1.0
     */
    public function setApplicationSitePaths()
    {
        $this->site_base_url = BASE_URL;

        $path = $this->application_path;

        $this->application_base_url = BASE_URL . $path;

        if (defined('SITE_NAME')) {
        } else {
            define('SITE_NAME', $this->parameters['site_name']);
        }

        if (defined('SITE_CACHE_FOLDER')) {
        } else {
            define('SITE_CACHE_FOLDER', SITE_BASE_PATH . '/' . $this->parameters['system_cache_folder']);
        }

        if (defined('SITE_LOGS_FOLDER')) {
        } else {
            define('SITE_LOGS_FOLDER', SITE_BASE_PATH . '/' . $this->parameters['system_logs_folder']);
        }

        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', SITE_BASE_PATH . '/' . $this->parameters['system_media_folder']);
        }
        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', SITE_BASE_URL_RESOURCES . '/' . $this->parameters['system_media_url']);
        }

        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', SITE_BASE_PATH . '/' . $this->parameters['system_temp_folder']);
        }

        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', SITE_BASE_URL_RESOURCES . '/' . $this->parameters['system_temp_url']);
        }

        return;
    }
}
