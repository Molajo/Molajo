<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * MolajoConfiguration
 *
 * @package     Molajo
 * @subpackage  Configuration
 * @since       1.0
 */
class MolajoConfiguration
{
    /**
     * Combined Site and Application Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public $config;

    /**
     * Site Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public $siteConfig;

    /**
     * Application Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public $appConfig;

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        $this->config = new JRegistry;
        $this->siteConfig = new JRegistry;
        $this->appConfig = new JRegistry;
    }

    /**
     * get
     *
     * Retrieves and combines site and application configuration objects
     *
     * Returns the global configuration object, creating it
     * if it doesn't already exist.
     *
     * @return configuration object
     * @throws RuntimeException
     * @since  1.0
     */
    public function getConfig()
    {
        /** Combined */
        $configData = $this->_createConfig();

        /** Site */
        $siteConfigData = $this->site();
        foreach ($siteConfigData as $key=>$value) {
            $this->set($key, $value, 'site');
        }

        /** Application */
        $appConfigData = $this->_application();
        foreach ($appConfigData as $key=>$value) {
            $this->set($key, $value, 'application');
        }

        /** combined populated */
        return $this->config;
    }

    /**
     * site
     *
     * retrieve site configuration object
     *
     * @return bool
     * @throws RuntimeException
     * @since  1.0
     */
    public function site()
    {
        $siteConfigData = array();

        $file = MOLAJO_SITE_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application-Site Configuration File does not exist');
        }

        $siteConfigData = new MolajoConfigSite();
        return $siteConfigData;
    }

    /**
     * application
     *
     * retrieve application configuration object
     *
     * @return bool
     * @throws RuntimeException
     * @since  1.0
     */
    protected function _application()
    {
        $appConfigData = array();

        $file = MOLAJO_APPLICATION_PATH . '/configuration.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new RuntimeException('Fatal error - Application Configuration File does not exist');
        }

        $appConfigData = new MolajoConfigApplication();
        return $appConfigData;
    }

    /**
     * _createConfig
     *
     * Create an empty configuration object that will store the combined Site and Application objects
     *
     * @return bool
     * @throws RuntimeException
     * @since   1.0
     */
    protected function _createConfig()
    {
        $configData = array();

        $file = MOLAJO_APPLICATION_CORE . '/configuration.php';
        if (is_file($file)) {
            include_once $file;
        } else {
            throw new RuntimeException('Fatal error - Configuration File does not exist '.$file);
        }

        $configData = new MolajoConfig();
        return $configData;
    }

    /**
     * get
     *
     * Returns a property of the Configuration object
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     * @param   string  $configFile Either site or application
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   1.0
     */
    public function get($key, $default = null, $configFile = null)
    {
        if ($configFile == 'application') {
            return $this->appConfig->get($key, $default);
        } else if ($configFile == 'site') {
            return $this->siteConfig->get($key, $default);
        } else {
            return $this->config->get($key, $default);
        }
    }

    /**
     * set
     *
     * Modifies a property of the configuration object, creating it if it does not already exist.
     *
     * @param   string  $key    The name of the property.
     * @param   mixed   $value  The value of the property to set (optional).
     *
     * @return  mixed   Previous value of the property
     *
     * @since   11.3
     */
    public function set($key, $value = null)
    {
        $this->config->set($key, $value);
    }
}
