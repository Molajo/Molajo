<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application\Service;

defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class Configuration
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * Configuration for Site and Application
     *
     * @static
     * @var    $connection
     * @since  1.0
     */
    protected $configuration = array();

    /**
     * Custom Fields
     *
     * @static
     * @var    $custom_fields
     * @since  1.0
     */
    protected $custom_fields = array();

    /**
     * Metadata
     *
     * @static
     * @var    $metadata
     * @since  1.0
     */
    protected $metadata = array();

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new ConfigurationService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * @return  object
     * @throws  RuntimeException
     * @since   1.0
     */
    protected function __construct()
    {
        $this->getConfiguration();
    }

    /**
     * getConfiguration
     *
     * Retrieves and combines site and application configuration objects
     *
     * @return  object
     * @throws  RuntimeException
     * @since   1.0
     */
    protected function getConfiguration()
    {
        $this->configuration = new Registry;
        $siteData = new Registry;

        /** Site Configuration: php file */
        $siteData = $this->getSite();
        foreach ($siteData as $key => $value) {
            $this->set($key, $value);
        }

        /** Application Table entry for each application - parameters field has config */
        $appConfig = $this->getApplicationInfo();

        $this->metadata = new Registry;
        $this->metadata->loadString($appConfig->metadata);

        $this->custom_fields = new Registry;
        $this->custom_fields->loadString($appConfig->custom_fields);

        // todo: amy check this after the interface is working and not test data
        $parameters = substr($appConfig->parameters, 1, strlen($appConfig->parameters) - 2);
        $parameters = substr($parameters, 0, strlen($parameters) - 1);
        $parmArray = array();
        $parmArray = explode(',', $parameters);
        foreach ($parmArray as $entry) {
            $pair = explode(':', $entry);
            $key = substr(trim($pair[0]), 1, strlen(trim($pair[0])) - 2);
            if (trim($pair[0]) == '') {
            } else {
                $value = substr(trim($pair[1]), 1, strlen(trim($pair[1])) - 2);
                $this->set($key, $value);
            }
        }
        return $this;
    }

    /**
     * get
     *
     * Retrieves a parameter value from the site/application configuration file
     *
     * Example usage:
     * $row->title = Service::Configuration()->get('site_title', 'Molajo');
     *
     * @param  string  $key
     * @param  string  $default
     *
     * @return  mixed
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return $this->configuration->get($key, $default);
    }

    /**
     * set
     *
     * Sets a value in the Site/Application Configuration
     *
     * Example usage:
     * Service::Configuration()->set('sef', 1);
     *
     * @param  string  $key
     * @param  mixed   $value
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        return $this->configuration->set($key, $value);
    }

    /**
     * getSite
     *
     * retrieve site configuration object from ini file
     *
     * @return object
     * @throws RuntimeException
     * @since  1.0
     */
    protected function getSite()
    {
        $siteConfigData = array();

        $file = SITE_FOLDER_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        $siteConfigData = new SiteConfiguration();
        return $siteConfigData;
    }

    /**
     * getApplicationInfo
     *
     * @return  boolean
     * @since   1.0
     */
    public function getApplicationInfo()
    {
        $row = new stdClass();

        if (MOLAJO_APPLICATION == 'installation') {

            $id = 0;
            $row->id = 0;
            $row->name = MOLAJO_APPLICATION;
            $row->path = MOLAJO_APPLICATION;
            $row->asset_type_id = MOLAJO_ASSET_TYPE_BASE_APPLICATION;
            $row->description = '';
            $row->custom_fields = '';
            $row->parameters = '';
            $row->metadata = '';

        } else {

            $m = new ApplicationsModel ();
            $m->query->where($m->db->qn('name') .
                ' = ' . $m->db->q(MOLAJO_APPLICATION));
            $result = $m->loadObject();

            $row->id = $result->id;
            $id = $result->id;
            $row->name = $result->name;
            $row->path = $result->path;
            $row->asset_type_id = $result->asset_type_id;
            $row->description = $result->description;
            $row->custom_fields = $result->custom_fields;
            $row->parameters = $result->parameters;
            $row->metadata = $result->metadata;
        }

        if (defined('MOLAJO_APPLICATION_ID')) {
        } else {
            define('MOLAJO_APPLICATION_ID', $id);
        }
        return $row;
    }
}

