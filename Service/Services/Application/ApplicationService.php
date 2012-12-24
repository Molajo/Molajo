<?php
/**
 * Application Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2012 Amy Stephen. All rights reserved.
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
 * @copyright  2012 Amy Stephen. All rights reserved.
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
    public $requested_resource_for_route = null;

    /**
     * Base URL Path for Application
     *
     * @var    string
     * @since  1.0
     */
    public $base_url_path_for_application = null;

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
        'base_url_path_for_application'
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
        $key = strtolower($key);

        echo $key . '<br />';

        if (in_array($key, $this->parameter_properties_array)) {
            echo 'yes'. '<br />';
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

        $this->set('base_url_path_for_application', $this->base_url_path_with_scheme . '/' . APPLICATION_URL_PATH);

        return;
    }
}
