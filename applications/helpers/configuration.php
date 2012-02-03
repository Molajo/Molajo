<?php
/**
 * @package     Molajo
 * @subpackage  Application Helper
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
class MolajoConfigurationHelper
{
    /**
     * Combined Site and Application Configuration Object
     *
     * @var    object
     * @since  1.0
     */
    public $config;

    /**
     * Site Configuration Object from fine
     *
     * @var    object
     * @since  1.0
     */
    public $siteConfig;

    /**
     * Application Configuration Object from database
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
    public function __construct($appConfig = null)
    {
        $this->config = new JRegistry;
        $this->siteConfig = new JRegistry;
        $this->appConfig = $appConfig;
    }

    /**
     * get
     *
     * Retrieves and combines site and application configuration objects
     *
     * @return object
     * @throws RuntimeException
     * @since  1.0
     */
    public function getConfig()
    {
        $configData = $this->site();

        /** Populate Configuration with Application Parameters from Database */
        $temp = substr($this->appConfig, 1, strlen($this->appConfig) - 2);
        $tempArray = array();
        $tempArray = explode(',', $temp);
        foreach ($tempArray as $entry) {
            $pair = explode(':', $entry);
            $key = substr(trim($pair[0]), 1, strlen(trim($pair[0])) - 2);
            if (trim($pair[0]) == '') {
            } else {
                $value = substr(trim($pair[1]), 1, strlen(trim($pair[1])) - 2);
                $this->set($key, $value, 'application');
            }
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
    public function get($key, $default = null, $type = null)
    {
        if ($type == 'site') {
            return $this->siteConfig->get($key, $default);
        } else {
            return $this->config->get($key, $default);
        }
    }

    /**
     * set
     *
     * Modifies a property of the configuration object
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
