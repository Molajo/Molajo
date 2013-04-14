<?php
/**
 * Application Service
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

use Exception;
use Molajo\Application\Exception\ApplicationException;
use Molajo\Application\Api\ApplicationInterface;

/**
 * Application Services
 *
 * 1. Identifies the current Application
 * 2. Load Application Application
 * 3. Defines Site Paths for Application
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 *
 * Usage:
 *
 *  To retrieve Application data for the Application:
 *
 *  $this->application_instance->get($key);
 *  $this->application_instance->set($key, $value);
 *
 *  System Class, not a Frontend Developer Resource
 */
class Application implements ApplicationInterface
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
    protected $request_url = null;

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
     * Controller Class Namespace
     *
     * @var    string
     * @since  1.0
     */
    protected $controller_class_namespace = false;

    /**
     * Application Application Data
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
     * Application Option for Forcing SSL
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
        'request_url',
        'calling_class',
        'calling_method',
        'controller_class_namespace',
        'parameters',
        'request_uri',
        'request_using_ssl',
        'requested_resource_for_route',
        'site_base_url',
        'url_force_ssl'
    );

    /**
     * class constructor
     *
     * @since   1.0
     */
    public function __construct($controller_class_namespace = 'Molajo\\Mvc\\Controller\\Controller')
    {
        $trace = debug_backtrace();
        if (isset($trace[1])) {
            $this->set('calling_class', $trace[1]['class']);
            $this->set('calling_method', $trace[1]['function']);
        }

        if ($controller_class_namespace == '') {
            $controller_class_namespace = 'Molajo\\Mvc\\Controller\\Controller';
        }

        $this->controller_class_namespace = $controller_class_namespace;

        return;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  ApplicationException
     */
    public function get($key, $default = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {

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
     * Parameters are set in the Application file or by updating the entire $parameters array
     *  and passing it back in - as a full array
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  ApplicationException
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
     * @return  $this
     * @since   1.0
     */
    public function setApplication()
    {
        if (strpos($this->request_uri, '/') == 0) {
            $this->request_uri = substr($this->request_uri, 1, 99999);
        }

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

        return $this;
    }

    /**
     * Append Application Node to Scheme + Base URL for use creating URLs for the Application
     *
     * @return  $this
     * @since   1.0
     */
    public function setBaseUrlPath()
    {
        $this->base_url_path_for_application = $this->site_base_url . '/' . APPLICATION_URL_PATH;

        return $this;
    }

    /**
     * Determine if the Application must use SSL, according to Application Data
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
        } else {

            $this->url_force_ssl = 0;
        }

        return false;
    }

    /**
     * Retrieve Application Data
     *
     * @return  $this
     * @since   1.0
     * @throws  ApplicationException
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
                $controller_class = $this->controller_class_namespace;
                $controller       = new $controller_class();
                $controller->getModelRegistry('Datasource', 'Application', 1);

                $controller->set('name_key_value', APPLICATION, 'model_registry');

                $item = $controller->getData(QUERY_OBJECT_ITEM);

                if ($item === false) {
                    throw new ApplicationException ('Application: Error executing getApplication Query');
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

            } catch (Exception $e) {
                throw new ApplicationException
                ('Application: Exception caught in Application: ' . $e->getMessage());
            }
        }

        return;
    }

    /**
     * Establish Site paths for media, cache, log, etc., locations as configured for this Application
     *
     * @return  $this
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

        return $this;
    }
}
