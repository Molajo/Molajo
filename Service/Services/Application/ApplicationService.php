<?php
/**
 * Application Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Application;

use Molajo\Service\Services;

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
     * Request URI
     *
     * @var    string
     * @since  1.0
     */
    protected $request_uri = null;

    /**
     * Site Base URL with Scheme
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url_path_with_scheme = null;

    /**
     * Applications XML identifying applications for this implementation
     *
     * @var    object
     * @since  1.0
     */
    protected $applications = null;

    /**
     * Resource portion of the URL for Route
     *
     * @var    string
     * @since  1.0
     */
    protected $requested_resource_for_route = null;

    /**
     * Base URL Path for Application
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url_path_for_application = null;

    /**
     * Configuration Option for Forcing SSL
     *
     * @var    string
     * @since  1.0
     */
    protected $url_force_ssl = null;

    /**
     * Request using SSL
     *
     * @var    bool
     * @since  1.0
     */
    protected $request_using_ssl = false;

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
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $properties_array = array(
        'parameters',
        'request_uri',
        'applications',
        'base_url_path_with_scheme',
        'requested_resource_for_route',
        'base_url_path_for_application',
        'url_force_ssl',
        'request_using_ssl',
        'calling_class',
        'calling_method'
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
     * Initialise
     *
     * @return  void
     * @since   1.0
     */
    public function initialise()
    {

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
    public function get($key, $default = null)
    {
        $key = strtolower($key);

        if ($key == 'parameters') {
        } else {
            if (in_array($key, $this->properties_array)) {

                if (isset($this->$key)) {
                    $this->$key = $default;
                }

                return $this->$key;

            }
        }
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }

        $this->parameters[$key] = $default;

        return $this->parameters[$key];
    }

    /**
     * Set the value of the specified key
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value)
    {
        $key = strtolower($key);

        if ($key == 'parameters') {
        } else {
            if (in_array($key, $this->properties_array)) {
                $this->$key = $value;

                return $this->$key;
            }
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

            $this->set('application_id', 0);
            $this->set('application_catalog_type_id', CATALOG_TYPE_APPLICATION);
            $this->set('application_name', APPLICATION);
            $this->set('application_description', APPLICATION);
            $this->set('application_path', APPLICATION);

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

                $this->set('application_id', (int)$item->id);
                $this->set('application_catalog_type_id', (int)$item->catalog_type_id);
                $this->set('application_name', $item->name);
                $this->set('application_path', $item->path);
                $this->set('application_description', $item->description);

                $parameters = $controller->getAdditionalData('Parameters');
                foreach ($parameters as $key => $value) {
                    $this->set($key, $value);
                }

                $metadata = $controller->getAdditionalData('Metadata');
                if (count($metadata) > 0) {
                    foreach ($metadata as $key => $value) {
                        $this->set('metadata_' . $key, $value);
                    }
                }

            } catch (\Exception $e) {
                throw new \Exception('Configuration: Exception caught in Configuration: ' . $e->getMessage());
            }
        }

        ksort($this->parameters);

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
        $this->set('site_base_url', BASE_URL);

        $path = $this->get('application_path', '');
        $this->set('application_base_url', BASE_URL . $path);

        if (defined('SITE_NAME')) {
        } else {
            define('SITE_NAME',
            $this->get('site_name', SITE_ID));
        }

        if (defined('SITE_CACHE_FOLDER')) {
        } else {
            define('SITE_CACHE_FOLDER', SITE_BASE_PATH
                . '/' . $this->get('system_cache_folder', 'cache'));
        }
        if (defined('SITE_LOGS_FOLDER')) {
        } else {

            define('SITE_LOGS_FOLDER', SITE_BASE_PATH
                . '/' . $this->get('system_logs_folder', 'logs'));
        }

        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', SITE_BASE_PATH
                . '/' . $this->get('system_media_folder', 'media'));
        }
        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', SITE_BASE_URL_RESOURCES
                . '/' . $this->get('system_media_url', 'media'));
        }

        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', SITE_BASE_PATH
                . '/' . $this->get('system_temp_folder', SITE_BASE_PATH . '/temp'));
        }

        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', SITE_BASE_URL_RESOURCES
                . '/' . $this->get('system_temp_url', 'temp'));
        }

        return;
    }
}
