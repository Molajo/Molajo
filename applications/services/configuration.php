<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Configuration
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoConfigurationService
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * Create the combined file once, and reuse
     *
     * @static
     *
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new MolajoConfigurationService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct() {}

    /**
     * connect
     *
     * Retrieves and combines site and application configuration objects
     *
     * @return  object
     * @throws  RuntimeException
     * @since   1.0
     */
    public function connect()
    {
        $config = new Registry;
        $siteConfig = new Registry;

        /** Site Configuration: php file */
        $configData = $this->getSite();
        foreach ($configData as $key => $value) {
            $this->set($key, $value);
        }

        /** Application Table entry for each application - parameters field has config */
        $appConfig = ApplicationHelper::getApplicationInfo(MOLAJO_APPLICATION_ID);
        if ($appConfig === false) {
            // error fail
            return false;
        }

        $this->_metadata = new Registry;
        $this->_metadata->loadString($appConfig->metadata);

        $this->_custom_fields = new Registry;
        $this->_custom_fields->loadString($appConfig->custom_fields);

        $cc = new ConfigurationService();
        $this->_config = $cc->getConfig($appConfig->parameters);

//todo: amy replace profiler
        $this->set('application.datetime', gmdate('Y-m-d H:i:s'));
        $this->set('application.timestamp', time());


        $temp = substr($appConfig, 1, strlen($appConfig) - 2);
        $tempArray = array();
        $tempArray = explode(',', $temp);
        foreach ($tempArray as $entry) {
            $pair = explode(':', $entry);
            $key = substr(trim($pair[0]), 1, strlen(trim($pair[0])) - 2);
            if (trim($pair[0]) == '') {
            } else {
                $value = substr(trim($pair[1]), 1, strlen(trim($pair[1])) - 2);
                $this->set($key, $value);
            }
        }

        /** combined populated */
        return $config;
    }

    /**
     * getSite
     *
     * retrieve site configuration object from ini file
     *
     * In some cases (ex. DB and Mail connections), the site configuration data
     * is required before the Application object is instantiated. in those cases,
     * the site file is directly accessed since this combined data is not yet
     * available.
     *
     * @return object
     * @throws RuntimeException
     * @since  1.0
     */
    public function getSite()
    {
        $siteConfigData = array();

        $file = MOLAJO_SITE_FOLDER_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        $siteConfigData = new MolajoSiteConfiguration();
        return $siteConfigData;
    }


    /**
     * get
     *
     * Retrieves values, or establishes the value with a default, if not available
     *
     * @param  string  $key      The name of the property.
     * @param  string  $default  The default value (optional) if none is set.
     * @param  string  $type     custom, metadata, languageObject, config
     *
     * @return  mixed
     *
     * @since   1.0
     */
    public function get($key, $default = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->get($key, $default);

        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $default);

        } else if ($key == 'logging') {
            return $this->_input;

        } else if ($key == 'input') {
            return $this->_input;

        } else {
            return $this->_config->get($key, $default);
        }
    }

    /**
     * set
     *
     * Modifies a property, creating it and establishing a default if not existing
     *
     * @param  string  $key    The name of the property.
     * @param  mixed   $value  The default value to use if not set (optional).
     * @param  string  $type   Custom, metadata, config
     *
     * @return  mixed
     * @since   1.0
     */
    public function set($key, $value = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->set($key, $value);

        } else if ($type == 'metadata') {
            return $this->_metadata->set($key, $value);

        } else if ($type == 'logging') {
            return $this->_metadata->set($key, $value);

        } else {
            return $this->_config->set($key, $value);
        }
    }

}
