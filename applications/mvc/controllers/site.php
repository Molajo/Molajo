<?php
/**
 * @package     Molajo
 * @subpackage  Site
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * MolajoControllerSite
 *
 * Acts as a Factory class for site specific functions and API options
 */
class MolajoControllerSite extends JObject
{
    /**
     * Configuration for Site
     *
     * @var    integer
     * @since  1.0
     */
    static public $config = null;

    /**
     * The base url
     *
     * @var    string
     * @since  1.0
     */
    static public $base_url = null;

    /**
     * Applications the site is authorized to access
     *
     * @var    string
     * @since  1.0
     */
    static public $applications = null;

    /**
     * Parameters
     *
     * @var    date
     * @since  1.0
     */
    static public $parameters = null;

    /**
     * Custom Fields
     *
     * @var    date
     * @since  1.0
     */
    static public $custom_fields = null;

    /**
     * getInstance
     *
     * Returns the global site object, creating if not existing
     *
     * @param   strong  $prefix Prefix for class names
     *
     * @return  site object
     *
     * @since  1.0
     */
    public static function getInstance($id = null, $config = array(), $prefix = 'Molajo')
    {
        static $instances;

        if (isset($instances)) {
        } else {
            $instances = array();
        }
        if (empty($instances[$id])) {

            $info = MolajoSiteHelper::getSiteInfo($id);
            if ($info === false) {
                return false;
            }

            $instance = new MolajoControllerSite();
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
            self::$config = $config;
        } else {
            self::$config = new JRegistry;
        }
    }

    /**
     * load
     *
     * Retrieves the configuration information, loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @since 1.0
     */
    public function load()
    {
        $info = MolajoSiteHelper::getSiteInfo();
        if ($info === false) {
            return false;
        }

        self::$parameters = $info->parameters;
        self::$custom_fields = $info->custom_fields;
        self::$base_url = $info->base_url;

        self::SetPaths();
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
        self::$applications = MolajoSiteHelper::getSiteApplications();
        if (self::$applications === false) {
            return false;
        }

        $found = false;
        foreach (self::$applications as $single) {
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
        if (defined('MOLAJO_SITE_FOLDER_PATH_CACHE')) {
        } else {
            define('MOLAJO_SITE_FOLDER_PATH_CACHE', self::get('cache_path', MOLAJO_SITE_FOLDER_PATH . '/cache'));
        }
        if (defined('MOLAJO_SITE_FOLDER_PATH_LOGS')) {
        } else {
            define('MOLAJO_SITE_FOLDER_PATH_LOGS', self::get('logs_path', MOLAJO_SITE_FOLDER_PATH . '/logs'));
        }
        if (defined('MOLAJO_SITE_FOLDER_PATH_MEDIA')) {
        } else {
            define('MOLAJO_SITE_FOLDER_PATH_MEDIA', self::get('media_path', MOLAJO_SITE_FOLDER_PATH . '/media'));
        }
        if (defined('MOLAJO_SITE_FOLDER_PATH_MEDIA_URL')) {
        } else {
            define('MOLAJO_SITE_FOLDER_PATH_MEDIA_URL', MOLAJO_BASE_URL . 'sites/' . MOLAJO_SITE_ID . '/media');
        }
        if (defined('MOLAJO_SITE_FOLDER_PATH_TEMP')) {
        } else {
            define('MOLAJO_SITE_FOLDER_PATH_TEMP', self::get('temp_path', MOLAJO_SITE_FOLDER_PATH . '/temp'));
        }
    }

    /**
     * siteConfig
     *
     * Creates the Site Configuration object.
     *
     * return   object  A config object
     *
     * @since  1.0
     */
    public function siteConfig()
    {
        $siteConfig = new MolajoConfigurationHelper ();
        $data = $siteConfig->site();

        if (is_array($data)) {
            self::$config->loadArray($data);

        } elseif (is_object($data)) {
            self::$config->loadObject($data);
        }

        return self::$config;
    }

    /**
     * get
     *
     * Returns a value for the Application object
     *
     * @param   string  $key
     * @param   mixed   $default
     *
     * @since   1.0
     */
    public function get($key, $default = null)
    {
        return self::$config->get($key, $default);
    }

    /**
     * set
     *
     * Set Value for the Application object
     *
     * @param   string  $key
     * @param   mixed   $value
     *
     * @since   1.0
     */
    public function set($key, $value = null)
    {
        self::$config->set($key, $value);
    }
}