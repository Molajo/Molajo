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
 * 1. Application Identification
 * 2. Installation
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
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
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $parameter_properties_array = array(
        'request_uri',
        'applications',
        'base_url_path_with_scheme',
        'requested_resource_for_route',
        'base_url_path_for_application',
        'url_force_ssl',
        'request_using_ssl'
    );

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key = null, $default = null)
    {
        if (in_array($key, $this->parameter_properties_array)) {

        } else {
            throw new \OutOfRangeException
            ('Site Service: attempting to get value for unknown property: ' . $key);
        }

        if (isset($this->$key)) {
            return $this->$key;
        }

        $this->$key = $default;
        return $this->$key;
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
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException
            ('Site Service: attempting to set value for unknown key: ' . $key);
        }

        $this->$key = $value;
        return $this->$key;
    }

    /**
     * Identify current application and page request
     *
     * @return  boolean
     * @since   1.0
     */
    public function setApplication()
    {
        if (strpos($this->request_uri, '/')) {
            $applicationTest = substr($this->request_uri, 0, strpos($this->request_uri, '/'));
        } else {
            $applicationTest = $this->request_uri;
        }

        $requested_resource_for_route = '';

        if (defined('APPLICATION')) {
            /* to override - must also define $this->request->get('requested_resource_for_route') */
        } else {

            foreach ($this->applications->application as $app) {

                $xml_name = (string)$app->name;

                if (strtolower(trim($xml_name)) == strtolower(trim($applicationTest))) {

                    define('APPLICATION', $app->name);
                    define('APPLICATION_URL_PATH', APPLICATION . '/');
                    define('APPLICATION_ID', $app->id);

                    $requested_resource_for_route = substr(
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
                define('APPLICATION_URL_PATH', '');
                define('APPLICATION_ID', $this->applications->default->id);

                $requested_resource_for_route = $this->request_uri;
            }
        }

        /*  Page Request used in Application::Request */
        if (strripos($requested_resource_for_route, '/') == (strlen($requested_resource_for_route) - 1)) {
            $requested_resource_for_route
                = substr($requested_resource_for_route, 0, strripos($requested_resource_for_route, '/'));
        }

        $this->set('requested_resource_for_route', $requested_resource_for_route);

        return;
    }

    /**
     * Append Application to Base URL with Scheme for use setting links
     *
     * @return  void
     * @since   1.0
     */
    public function setBaseUrlPathforApplication ()
    {
        $this->set('base_url_path_for_application', $this->base_url_path_with_scheme . '/' . APPLICATION_URL_PATH);

        return;
    }

    /**
     * Check to see if secure access to the application is required by configuration
     *
     * @return  void
     * @since   1.0
     */
    protected function sslCheck()
    {
        if ((int)$this->get('url_force_ssl', 0) > 0) {

            if (($this->get('request_using_ssl') === true)) {

            } else {

                $redirectTo = (string)'https' .
                    substr(BASE_URL, 4, strlen(BASE_URL) - 4) .
                    APPLICATION_URL_PATH .
                    '/' . $this->get('requested_resource_for_route');

                Services::Redirect()
                    ->set($redirectTo, 301);
            }
        }

        return;
    }

    /**
     * Get the application data and store it in the registry
     *
     * @return  ConfigurationService
     * @since   1.0
     * @throws  \Exception
     */
    public function getApplication()
    {
        if (APPLICATION == 'installation') {

            Services::Registry()->set('Configuration', 'application_id', 0);
            Services::Registry()->set('Configuration', 'application_catalog_type_id', CATALOG_TYPE_APPLICATION);
            Services::Registry()->set('Configuration', 'application_name', APPLICATION);
            Services::Registry()->set('Configuration', 'application_description', APPLICATION);
            Services::Registry()->set('Configuration', 'application_path', APPLICATION);

        } else {

            try {
                $profiler_service = 0;

                $controllerClass = CONTROLLER_CLASS;
                $controller = new $controllerClass();
                $controller->getModelRegistry('Datasource', 'Application', 1);
                $controller->set('name_key_value', APPLICATION, 'model_registry');
                $item = $controller->getData(QUERY_OBJECT_ITEM);
                if ($item === false) {
                    throw new \Exception ('ConfigurationService: Error executing getApplication Query');
                }

                Services::Registry()->set('Configuration', 'application_id', (int)$item->id);
                Services::Registry()->set(
                    'Configuration',
                    'application_catalog_type_id',
                    (int)$item->catalog_type_id
                );
                Services::Registry()->set('Configuration', 'application_name', $item->name);
                Services::Registry()->set('Configuration', 'application_path', $item->path);
                Services::Registry()->set('Configuration', 'application_description', $item->description);

                $profiler_service = 0;

                $parameters = Services::Registry()->getArray('ApplicationDatasourceParameters');
                foreach ($parameters as $key => $value) {
                    Services::Registry()->set('Configuration', $key, $value);
                }

                $metadata = Services::Registry()->getArray('ApplicationDatasourceMetadata');
                if (count($metadata) > 0) {
                    foreach ($metadata as $key => $value) {
                        Services::Registry()->set('Configuration', 'metadata_' . $key, $value);
                    }
                }

            } catch (\Exception $e) {
                throw new \Exception('Configuration: Exception caught in Configuration: '. $e->getMessage());
            }
        }

        Services::Registry()->sort('Configuration');

        return $this;
    }

    /**
     * Establish media, cache, log, etc., locations for site for application use
     *
     * Called out of the Configurations Class construct - paths needed in startup process for other services
     *
     * @return  mixed
     * @since   1.0
     */
    protected function setApplicationSitePaths()
    {
        Services::Registry()->set('Configuration', 'site_base_url', BASE_URL);

        $path = Services::Registry()->get('Configuration', 'application_path', '');
        Services::Registry()->set('Configuration', 'application_base_url', BASE_URL . $path);

        if (defined('SITE_NAME')) {
        } else {
            define('SITE_NAME',
            Services::Registry()->get('Configuration', 'site_name', SITE_ID));
        }

        if (defined('SITE_CACHE_FOLDER')) {
        } else {
            define('SITE_CACHE_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get('Configuration', 'system_cache_folder', 'cache'));
        }
        if (defined('SITE_LOGS_FOLDER')) {
        } else {

            define('SITE_LOGS_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get('Configuration', 'system_logs_folder', 'logs'));
        }

        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get('Configuration', 'system_media_folder', 'media'));
        }
        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', SITE_BASE_URL_RESOURCES
                . '/' . Services::Registry()->get('Configuration', 'system_media_url', 'media'));
        }

        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', SITE_BASE_PATH
                . '/' . Services::Registry()->get(
                'Configuration',
                'system_temp_folder',
                SITE_BASE_PATH . '/temp'
            ));
        }

        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', SITE_BASE_URL_RESOURCES
                . '/' . Services::Registry()->get('Configuration', 'system_temp_url', 'temp'));
        }

        return true;
    }
}
