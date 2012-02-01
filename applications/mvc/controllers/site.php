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
class MolajoControllerSite
{
    /**
     * $config
     *
     * @var    integer
     * @since  1.0
     */
    static protected $_config = null;

    /**
     * $dbinfo
     *
     * @var    object
     * @since  1.0
     */
    static protected $_siteQueryResults = null;

    /**
     * $base_url
     *
     * @var    string
     * @since  1.0
     */
    static public $base_url = null;

    /**
     * $applications
     *
     * Applications the site is authorized to access
     *
     * @var    array
     * @since  1.0
     */
    static public $applications = null;

    /**
     * $parameters
     *
     * @var    array
     * @since  1.0
     */
    static public $parameters = null;

    /**
     * $custom_fields
     *
     * @var    array
     * @since  1.0
     */
    static protected $_custom_fields = null;

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

            $instance = new MolajoControllerSite($info);
            $instances[$id] = &$instance;
            self::siteConfig();
        }

        return $instances[$id];
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @param  object $dbinfo
     *
     * @since  1.0
     */
    public function __construct($siteQueryResults = null)
    {
        self::$_config = new JRegistry;
        self::$_siteQueryResults = $siteQueryResults;
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
        $this->_custom_fields = new JRegistry;
        $this->_custom_fields->loadString(self::$_siteQueryResults->custom_fields);

        $this->_parameters = new JRegistry;
        $this->_parameters->loadString(self::$_siteQueryResults->parameters);

        $this->_metadata = new JRegistry;
        $this->_metadata->loadString(self::$_siteQueryResults->metadata);

        self::$base_url = self::$_siteQueryResults->base_url;

        self::_setPaths();
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
     * _setPaths
     *
     * Retrieves site configuration information and sets paths for site file locations
     *
     * @results  null
     * @since    1.0
     */
    public function _setPaths()
    {
        if (defined('MOLAJO_SITE_NAME')) {
        } else {
            define('MOLAJO_SITE_NAME', self::get('site_name', MOLAJO_SITE_ID));
        }
        if (defined('MOLAJO_SITE_CACHE_FOLDER')) {
        } else {
            define('MOLAJO_SITE_CACHE_FOLDER', self::get('cache_path', MOLAJO_SITE_FOLDER_PATH . '/cache'));
        }
        if (defined('MOLAJO_SITE_LOGS_FOLDER')) {
        } else {
            define('MOLAJO_SITE_LOGS_FOLDER', self::get('logs_path', MOLAJO_SITE_FOLDER_PATH . '/logs'));
        }

        /** following must be within the web document folder */
        if (defined('MOLAJO_SITE_MEDIA_FOLDER')) {
        } else {
            define('MOLAJO_SITE_MEDIA_FOLDER', self::get('media_path', MOLAJO_SITE_FOLDER_PATH . '/media'));
        }
        if (defined('MOLAJO_SITE_MEDIA_URL')) {
        } else {
            define('MOLAJO_SITE_MEDIA_URL', MOLAJO_BASE_URL . self::get('media_url', MOLAJO_BASE_URL . 'sites/' . MOLAJO_SITE_ID . '/media'));
        }
        if (defined('MOLAJO_SITE_TEMP_FOLDER')) {
        } else {
            define('MOLAJO_SITE_TEMP_FOLDER', self::get('temp_path', MOLAJO_SITE_FOLDER_PATH . '/temp'));
        }
        if (defined('MOLAJO_SITE_TEMP_URL')) {
        } else {
            define('MOLAJO_SITE_TEMP_URL', MOLAJO_BASE_URL . self::get('temp_url', MOLAJO_BASE_URL . 'sites/' . MOLAJO_SITE_ID . '/temp'));
        }

        return;
    }

    /**
     * siteConfig
     *
     * Creates the Site Configuration object.
     *
     * return  null
     * @since  1.0
     */
    public function siteConfig()
    {
        $siteConfig = new MolajoConfigurationHelper ();
        $data = $siteConfig->site();

        if (is_array($data)) {
            self::$_config->loadArray($data);

        } elseif (is_object($data)) {
            self::$_config->loadObject($data);
        }

        return;
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
     * @since   1.0
     */
    public function get($key, $default = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->get($key, $default);
        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $default);
        } else {
            return self::$_config->get($key, $default);
        }
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
     * @since   1.0
     */
    public function set($key, $value = null, $type = 'config')
    {
        if ($type == 'custom') {
            return $this->_custom_fields->set($key, $value);
        } else if ($type == 'metadata') {
            return $this->_metadata->get($key, $value);
        } else {
            return self::$_config->get($key, $value);
        }
    }
}
