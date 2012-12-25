<?php
/**
 * Site Service
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Service\Services\Site;

use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * Site Services
 *
 * 1. Site Identification
 * 2. Installation
 *
 * @author     Amy Stephen
 * @license    MIT
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @since      1.0
 */
Class SiteService
{
    /**
     * Base URL from Request
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url = null;

    /**
     * Site Base URL from Request
     *
     * @var    string
     * @since  1.0
     */
    protected $site_base_url = null;

    /**
     * Sites XML identifying sites on this implementation
     *
     * @var    object
     * @since  1.0
     */
    protected $sites = null;

    /**
     * Sites XML containing defines information
     *
     * @var    object
     * @since  1.0
     */
    protected $custom_defines = null;

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $parameter_properties_array = array(
        'base_url',
        'custom_defines',
        'site_base_url',
        'sites'
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

        if (in_array($key, $this->parameter_properties_array)) {
        } else {
            throw new \OutOfRangeException
            ('Site Service: attempting to get value for unknown property: ' . $key);
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
     * Populate BASE_URL using scheme, host, and base URL
     *
     * @return  void
     * @since   1.0
     */
    public function setBaseURL()
    {
        if (defined('BASE_URL')) {
        } else {
            /**
             * BASE_URL - root of the website with a trailing slash
             */
            define('BASE_URL', $this->base_url . '/');
        }

        if (defined('SITES')) {
        } else {
            define('SITES', BASE_FOLDER . '/Site');
        }

        if (defined('SITES_MEDIA_FOLDER')) {
        } else {
            define('SITES_MEDIA_FOLDER', SITES . '/media');
        }

        if (defined('SITES_MEDIA_URL')) {
        } else {
            define('SITES_MEDIA_URL', BASE_URL . 'Site/media');
        }

        if (defined('SITES_DATA_OBJECT_FOLDER')) {
        } else {
            define('SITES_DATA_OBJECT_FOLDER', BASE_URL . 'Site/media');
        }

        return;
    }

    /**
     * Identifies the specific site and sets site paths for use in the application
     *
     * @return  void
     * @since   1.0
     */
    public function identifySite()
    {
        if (defined('SITE_BASE_URL')) {
        } else {

            foreach ($this->sites->site as $single) {

                if (strtolower((string)$single->site_base_url) == strtolower($this->site_base_url)) {

                    define('SITE_BASE_PATH', BASE_FOLDER . (string)$single->site_base_folder);

                    if (APPLICATION == 'installation') {
                    } else {
                        $this->installCheck();
                    }

                    define('SITE_BASE_URL', (string)$single->site_base_url);
                    define('SITE_BASE_URL_RESOURCES', SITE_BASE_URL . (string)$single->site_base_folder);
                    define('SITE_DATA_OBJECT_FOLDER', SITE_BASE_PATH . '/' . 'Dataobject');
                    define('SITE_ID', $single->id);
                    define('SITE_NAME', $single->name);
                    break;
                }
            }

            if (defined('SITE_BASE_URL')) {
            } else {
                throw new \RuntimeException
                ('Sites Service: Cannot identify site for: ' . $this->site_base_url);
            }
        }

        return;
    }

    /**
     * Folders and subfolders can be relocated outside of the Apache htdocs for increased security.
     * To do so, create a defines file and override the Autoload.php file for the new namespaces.
     *
     * Note: SITES contains content that must be accessible by the Website and thus cannot be moved.
     *
     * @return  boolean
     * @since   1.0
     */
    public function setStandardDefines()
    {
        if (file_exists(BASE_FOLDER . '/defines.php')) {
            include_once BASE_FOLDER . '/defines.php';
        }

        if (defined('EXTENSIONS')) {
        } else {
            define('EXTENSIONS', BASE_FOLDER . '/Extension');
        }

        if (defined('EXTENSIONS_MENUITEMS')) {
        } else {
            define('EXTENSIONS_MENUITEMS', EXTENSIONS . '/Menuitem');
        }
        if (defined('EXTENSIONS_RESOURCES')) {
        } else {
            define('EXTENSIONS_RESOURCES', EXTENSIONS . '/Resource');
        }
        if (defined('EXTENSIONS_THEMES')) {
        } else {
            define('EXTENSIONS_THEMES', EXTENSIONS . '/Theme');
        }
        if (defined('EXTENSIONS_VIEWS')) {
        } else {
            define('EXTENSIONS_VIEWS', EXTENSIONS . '/View');
        }

        if (defined('EXTENSIONS_URL')) {
        } else {
            define('EXTENSIONS_URL', BASE_URL . 'Extension');
        }
        if (defined('EXTENSIONS_THEMES_URL')) {
        } else {
            define('EXTENSIONS_THEMES_URL', BASE_URL . 'Extension/Theme');
        }
        if (defined('EXTENSIONS_VIEWS_URL')) {
        } else {
            define('EXTENSIONS_VIEWS_URL', BASE_URL . 'Extension/View');
        }

        if (defined('SERVICES')) {
        } else {
            define('SERVICES', PLATFORM_FOLDER . '/Service');
        }
        if (defined('CORE_THEMES')) {
        } else {
            define('CORE_THEMES', PLATFORM_FOLDER . '/Theme');
        }
        if (defined('CORE_VIEWS')) {
        } else {
            define('CORE_VIEWS', PLATFORM_FOLDER . '/MVC/View');
        }
        if (defined('CORE_LANGUAGES')) {
        } else {
            define('CORE_LANGUAGES', PLATFORM_FOLDER . '/Language');
        }

        if (defined('CORE_SYSTEM_URL')) {
        } else {
            define('CORE_SYSTEM_URL', BASE_URL . 'Vendor/Molajo/System');
        }
        if (defined('CORE_THEMES_URL')) {
        } else {
            define('CORE_THEMES_URL', BASE_URL . 'Vendor/Molajo/Theme');
        }
        if (defined('CORE_VIEWS_URL')) {
        } else {
            define('CORE_VIEWS_URL', BASE_URL . 'Vendor/Molajo/MVC/View');
        }

        if (defined('SITES')) {
        } else {
            define('SITES', BASE_FOLDER . '/Site');
        }

        return true;
    }

    /**
     * Custom set of defines for consistency in Application
     *
     * @return  void
     * @since   1.0
     */
    public function setCustomDefines()
    {
        if (count($this->custom_defines) > 0) {
        } else {
            return;
        }

        foreach ($this->custom_defines->define as $item) {
            if (defined((string)$item['name'])) {
            } else {
                $value = (string)$item['value'];
                define((string)$item['name'], $value);
            }
        }

        return;
    }

    /**
     * Determine if the site has already been installed
     *
     * return  boolean
     * @since  1.0
     */
    public function installCheck()
    {
        if (defined('SKIP_INSTALL_CHECK')) {
            return;
        }

        if (file_exists(SITE_BASE_PATH . '/Dataobject/Database.xml')
            && filesize(SITE_BASE_PATH . '/Dataobject/Database.xml') > 10) {
            return;
        }

        $redirect = BASE_URL . 'installation/';
        header('Location: ' . $redirect);

        exit();
    }
}
