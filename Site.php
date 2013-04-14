<?php
/**
 * Site Service
 *
 * @package   Molajo
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 */
namespace Molajo\Site;

defined('MOLAJO') or die;

use stdClass;

use Molajo\Site\Exception\SiteException;

use Molajo\Site\Api\SiteInterface;

/**
 * Site Services
 *
 * 1. Site Identification
 * 2. Installation
 *
 * @author    Amy Stephen
 * @license   http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright 2013 Amy Stephen. All rights reserved.
 * @since     1.0
 */
class Site implements SiteInterface
{
    /**
     * Base URL from Request
     *
     * @var    string
     * @since  1.0
     */
    protected $base_url = null;

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
    protected $custom_defines = array();

    /**
     * List of Properties
     *
     * @var    object
     * @since  1.0
     */
    protected $property_array = array(
        'calling_class',
        'calling_method',
        'base_url',
        'custom_defines',
        'sites'
    );

    /**
     * Class constructor
     *
     * @since   1.0
     */
    public function __construct()
    {
        $trace = debug_backtrace();
        if (isset($trace[1])) {
            $this->set('calling_class', $trace[1]['class']);
            $this->set('calling_method', $trace[1]['function']);
        }

        return;
    }

    /**
     * Get the current value (or default) of the specified key
     *
     * @param   string $key
     * @param   mixed  $default
     *
     * @return  mixed
     * @since   1.0
     * @throws  SiteException
     */
    public function get($key = null, $default = null)
    {
        $key = strtolower($key);

        if ((string)$key === '') {
            $site = new stdClass();
            foreach ($this->property_array as $key) {
                $site->$key = $this->$key;
            }
            return $site;
        }

        if (in_array($key, $this->property_array)) {

        } else {

            throw new SiteException
            ('Site Service: attempting to get value for unknown property: ' . $key);
        }

        if ($this->$key === null) {
            $this->$key = $default;
        }

        return $this->$key;
    }

    /**
     * Set the value of the specified key
     *
     * @param   string $key
     * @param   mixed  $value
     *
     * @return  mixed
     * @since   1.0
     * @throws  SiteException
     */
    public function set($key, $value = null)
    {
        $key = strtolower($key);

        if (in_array($key, $this->property_array)) {
        } else {

            throw new SiteException
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
     * @throws  SiteInterface
     */
    public function identifySite()
    {
        if (defined('SITE_BASE_URL')) {
        } else {

            foreach ($this->sites->site as $single) {

                if (strtolower((string)$single->site_base_url) == strtolower($this->base_url)) {

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
                throw new SiteException
                ('Sites Service: Cannot identify site for: ' . $this->base_url);
            }
        }

        return;
    }

    /**
     * Custom set of defines for consistency in Application
     *
     * @return void
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
     *
     * @since  1.0
     */
    public function installCheck()
    {
        if (defined('SKIP_INSTALL_CHECK')) {
            return;
        }

        if (file_exists(SITE_BASE_PATH . '/Dataobject/Database.xml')
            && filesize(SITE_BASE_PATH . '/Dataobject/Database.xml') > 10
        ) {
            return;
        }

        $redirect = BASE_URL . 'installation/';
        header('Location: ' . $redirect);

        exit();
    }
}
