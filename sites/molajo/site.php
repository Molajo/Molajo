<?php
/**
 * @package     Molajo
 * @subpackage  Site
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoSite
 *
 * Acts as a Factory class for site specific functions and API options
 */
class MolajoSite extends JObject
{
    /**
     * Application configuration object.
     *
     * @var    integer
     * @since  1.0
     */
    public $config = null;

    /**
     * The base url
     *
     * @var    string
     * @since  1.0
     */
    public $base_url = null;

    /**
     * Applications the site is authorized to access
     *
     * @var    string
     * @since  1.0
     */
    public $applications = null;

    /**
     * Parameters
     *
     * @var    date
     * @since  1.0
     */
    public $parameters = null;

    /**
     * Parameters
     *
     * @var    date
     * @since  1.0
     */
    public $custom_fields = null;

    /**
     * getInstance
     *
     * Returns the global site object, creating if not existing
     *
     * @param   strong  $prefix       Prefix for class names
     *
     * @return  site object
     *
     * @since  1.0
     */
    public static function getInstance($prefix = 'Molajo')
    {
        static $instances;

        if (isset($instances)) {
        } else {
            $instances = array();
        }
        if (empty($instances[MOLAJO_SITE])) {

            $results = MolajoSiteHelper::loadSiteClasses();
            if ($results === false) {
                return false;
            }

            $info = MolajoSiteHelper::getSiteInfo();
            if ($info === false) {
                return false;
            }

            if (defined('MOLAJO_SITE_ID')) {
            } else {
                define('MOLAJO_SITE_ID', $info->id);
            }

            $classname = $prefix . ucfirst(MOLAJO_SITE) . 'Site';
            if (class_exists($classname)) {
                $instance = new $classname();
            } else {
                return MolajoError::raiseError(500, MolajoTextHelper::sprintf('MOLAJO_SITE_INSTANTIATION_ERROR', $classname));
            }
            $instances[MOLAJO_SITE] = &$instance;
        }

        return $instances[MOLAJO_SITE];
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param   array  $config  A configuration array
     *
     * @since  1.0
     */
    public function __construct($config = null)
    {
        if ($config) {
            $this->config = $config;
        } else {
            $this->config = new JRegistry;
        }
    }

    /**
     * initialise
     *
     * Retrieves the configuration information, loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @since 1.0
     */
    public function initialise($options = array())
    {
        $info = MolajoSiteHelper::getSiteInfo();
        if ($info === false) {
            return false;
        }

        $this->parameters = $info->parameters;
        $this->custom_fields = $info->custom_fields;
        $this->base_url = $info->base_url;

        $this->SetPaths();
    }

    /**
     * authorise
     *
     * Check if the site is authorized for this application
     *
     * @param $application_id
     * @return boolean
     */
    public function authorise($application_id)
    {
        $this->applications = MolajoSiteHelper::getSiteApplications();
        if ($this->applications === false) {
            return false;
        }

        $found = false;
        foreach ($this->applications as $single) {
            if ($single->application_id == $application_id) {
                $found = true;
            }
        }
        if ($found === true) {
            return true;
        }

        MolajoError::raiseError(403, MolajoTextHelper::_('SITE_NOT_AUTHORIZED_FOR_APPLICATION'));
        return false;
    }

    /**
     * _createSiteConfiguration
     *
     * Create the Site configuration registry.
     *
     * @param   string  $file  The path to the site configuration file
     *
     * return   object  A config object
     *
     * @since  1.0
     */
    protected function _createSiteConfiguration($file = null)
    {
        if ($file == null) {
        } else {
            require_once $file;
        }

        /** Site Configuration */
        $this->siteConfig = new MolajoConfigSite ();
        $registry = MolajoFactory::getSiteConfig();
        $registry->loadObject($this->siteConfig);
    }

    /**
     * setPaths
     *
     * Retrieves the configuration information and sets paths for site file locations
     *
     * @param    array
     *
     * @since 1.0
     */
    public function setPaths()
    {
        if (defined('MOLAJO_SITE_PATH_CACHE')) {
        } else {
            define('MOLAJO_SITE_PATH_CACHE', $this->getSiteConfig('cache_path', MOLAJO_SITE_PATH . '/cache'));
        }
        if (defined('MOLAJO_SITE_PATH_IMAGES')) {
        } else {
            define('MOLAJO_SITE_PATH_IMAGES', $this->getSiteConfig('images_path', MOLAJO_SITE_PATH . '/images'));
        }
        if (defined('MOLAJO_SITE_PATH_LOGS')) {
        } else {
            define('MOLAJO_SITE_PATH_LOGS', $this->getSiteConfig('logs_path', MOLAJO_SITE_PATH . '/logs'));
        }
        if (defined('MOLAJO_SITE_PATH_MEDIA')) {
        } else {
            define('MOLAJO_SITE_PATH_MEDIA', $this->getSiteConfig('media_path', MOLAJO_SITE_PATH . '/media'));
        }
        if (defined('MOLAJO_SITE_PATH_TMP')) {
        } else {
            define('MOLAJO_SITE_PATH_TMP', $this->getSiteConfig('tmp_path', MOLAJO_SITE_PATH . '/tmp'));
        }
    }

    /**
     * getSiteConfig
     *
     * Gets a configuration value.
     *
     * @param   string   The name of the value to get.
     * @param   string   Default value to return
     *
     * @return  mixed    The user state.
     *
     * @since  1.0
     */
    public function getSiteConfig($varname, $default = null)
    {
        return MolajoFactory::getSiteConfig()->get('' . $varname, $default);
    }


    /**
     * getConfig
     *
     * Creates the Application configuration object.
     *
     * return   object  A config object
     *
     * @since  1.0
     */
    public function getConfig()
    {
        $data = MolajoConfiguration::site();

        if (is_array($data)) {
            $this->config->loadArray($data);

        } elseif (is_object($data)) {
            $this->config->loadObject($data);
        }

        return $this->config;
    }

    /**
     * get
     *
     * Returns a property of the Application object
     * or the default value if the property is not set.
     *
     * @param   string  $key      The name of the property.
     * @param   mixed   $default  The default value (optional) if none is set.
     *
     * @return  mixed   The value of the configuration.
     *
     * @since   11.3
     */
    public function get($key, $default = null)
    {
        return $this->config->get($key, $default);
    }

    /**
     * set
     *
     * Modifies a property of the Application object, creating it if it does not already exist.
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
        $previous = $this->config->get($key);
        $this->config->set($key, $value);

        return $previous;
    }
}