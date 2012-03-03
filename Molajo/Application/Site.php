<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

defined('MOLAJO') or die;

use Molajo\Common\Version;
use Molajo\Application\Application;
use Molajo\Application\MVC\Model;

/**
 * Site
 *
 * @package     Molajo
 * @subpackage  Base
 * @since       1.0
 */
Class Site
{
    /**
     * $instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance = null;

    /**
     * $config
     *
     * @var    integer
     * @since  1.0
     */
    protected $config = null;

    /**
     * $parameters
     *
     * @var    array
     * @since  1.0
     */
    protected $parameters = null;

    /**
     * $custom_fields
     *
     * @var    array
     * @since  1.0
     */
    protected $custom_fields = null;

    /**
     * getInstance
     *
     * Returns the global site object, creating if not existing
     *
     * @return  site  object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (self::$instance) {
        } else {
            self::$instance = new Site ();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @since  1.0
     */
    public function __construct()
    {
        if (version_compare(PHP_VERSION, '5.3', '<')) {
            die('Your host needs to use PHP 5.3 or higher to run Molajo.');
        }

        $molajo = new Version();
        //        echo $molajo->VERSION;

        $this->_defines();

        $this->load();
    }

    /**
     * load
     *
     * Retrieves the configuration information, loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @return null
     * @since 1.0
     */
    public function load()
    {
        /** Instantiate Application */
        Molajo::Application()->initialize();

        /** Set Site Paths */
        $this->_setPaths();

        /** Site Parameters */
        $m = new SitesModel ();
        $m->query->where($m->db->qn('id') . ' = ' . (int)SITE_ID);
        $results = $m->runQuery();
        foreach ($results as $info)
        {
        }
        if ($info === false) {
            return;
        }

        /** is site authorised for this Application? */
        $authorise = Service::Access()->authoriseSiteApplication();
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        $this->custom_fields = new Registry;
        $this->custom_fields->loadString($info->custom_fields);

        $this->parameters = new Registry;
        $this->parameters->loadString($info->parameters);

        $this->metadata = new Registry;
        $this->metadata->loadString($info->metadata);

        $this->base_url = $info->base_url;

        /** Primary Application Logic Flow */
        $results = Molajo::Application()->process();

        /** Application Complete */
        if (Service::Configuration()->get('debug', 0) == 1) {
            debug('MolajoSite::load End');
        }

        exit(0);
    }

    protected function _defines()
    {
        /**
         *  Override folder locations using a new defines.php file
         *  on the base folder that identifies the following defines
         *  and update the SITES/sites.xml file folderpath values
         * VENDOR defined in Autoload.php
         */
        if (file_exists(MOLAJO_BASE_FOLDER . '/defines.php')) {
            include_once MOLAJO_BASE_FOLDER . '/defines.php';
        }

        if (defined('MOLAJO_APPLICATIONS')) {
        } else {
            define('MOLAJO_APPLICATIONS', MOLAJO_BASE_FOLDER . '/Molajo/Application');
        }
        if (defined('MOLAJO_EXTENSIONS')) {
        } else {
            define('MOLAJO_EXTENSIONS', MOLAJO_BASE_FOLDER . '/Molajo/Extension');
        }
        if (defined('SITES')) {
        } else {
            define('SITES', MOLAJO_BASE_FOLDER . '/sites');
        }
        $this->_identifySite();
        echo MOLAJO_SITE;
        /** Define PHP constants for application variables */
        $defines = simplexml_load_file(MOLAJO_APPLICATIONS . '/Configuration/defines.xml', 'SimpleXMLElement');

        foreach ($defines->define as $item) {
            if (defined((string)$item['name'])) {
            } else {
                $value = (string)$item['value'];
                define((string)$item['name'], $value);
            }
        }

        /**
         *  Platform
         */
        if (defined('PLATFORM_MOLAJO')) {
        } else {
            define('PLATFORM_MOLAJO', VENDOR . '/molajo');
        }

        /**
         *  Applications
         */
        if (defined('MOLAJO_APPLICATIONS_MVC')) {
        } else {
            define('MOLAJO_APPLICATIONS_MVC', MOLAJO_APPLICATIONS . '/base/mvc');
        }
        if (defined('MOLAJO_APPLICATIONS_MVC_URL')) {
        } else {
            define('MOLAJO_APPLICATIONS_MVC_URL', MOLAJO_BASE_URL . 'applications/base/mvc');
        }

        /**
         *  Extensions
         */
        if (defined('MOLAJO_EXTENSIONS_COMPONENTS')) {
        } else {
            define('MOLAJO_EXTENSIONS_COMPONENTS', MOLAJO_EXTENSIONS . '/components');
        }
        if (defined('MOLAJO_EXTENSIONS_FORMFIELDS')) {
        } else {
            define('MOLAJO_EXTENSIONS_FORMFIELDS', MOLAJO_EXTENSIONS . '/formfields');
        }
        if (defined('MOLAJO_EXTENSIONS_LANGUAGES')) {
        } else {
            define('MOLAJO_EXTENSIONS_LANGUAGES', MOLAJO_EXTENSIONS . '/languages');
        }
        if (defined('MOLAJO_EXTENSIONS_MODULES')) {
        } else {
            define('MOLAJO_EXTENSIONS_MODULES', MOLAJO_EXTENSIONS . '/modules');
        }
        if (defined('MOLAJO_EXTENSIONS_PLUGINS')) {
        } else {
            define('MOLAJO_EXTENSIONS_PLUGINS', MOLAJO_EXTENSIONS . '/plugins');
        }
        if (defined('MOLAJO_EXTENSIONS_THEMES')) {
        } else {
            define('MOLAJO_EXTENSIONS_THEMES', MOLAJO_EXTENSIONS . '/themes');
        }
        if (defined('MOLAJO_EXTENSIONS_VIEWS')) {
        } else {
            define('MOLAJO_EXTENSIONS_VIEWS', MOLAJO_EXTENSIONS . '/views');
        }

        if (defined('MOLAJO_EXTENSIONS_COMPONENTS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_COMPONENTS_URL', MOLAJO_BASE_URL . 'extensions/components');
        }
        if (defined('MOLAJO_EXTENSIONS_FORMFIELDS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_FORMFIELDS_URL', MOLAJO_BASE_URL . 'extensions/formfields');
        }
        if (defined('MOLAJO_EXTENSIONS_MODULES_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_MODULES_URL', MOLAJO_BASE_URL . 'extensions/modules');
        }
        if (defined('MOLAJO_EXTENSIONS_PLUGINS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_PLUGINS_URL', MOLAJO_BASE_URL . 'extensions/plugins');
        }
        if (defined('MOLAJO_EXTENSIONS_THEMES_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_THEMES_URL', MOLAJO_BASE_URL . 'extensions/themes');
        }
        if (defined('MOLAJO_EXTENSIONS_VIEWS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_VIEWS_URL', MOLAJO_BASE_URL . 'extensions/views');
        }

        /**
         *  Allows for quoting in language .ini files.
         */
        if (defined('MOLAJO_LANGUAGE_QUOTE_REPLACEMENT')) {
        } else {
            define('MOLAJO_LANGUAGE_QUOTE_REPLACEMENT', '"');
        }

        /**
         *  EXTENSION OPTIONS
         *
         *  SOON TO BE REMOVED
         */
        define('MOLAJO_EXTENSION_OPTION_ID_TABLE', 100);
        define('MOLAJO_EXTENSION_OPTION_ID_FIELDS', 200);
        define('MOLAJO_EXTENSION_OPTION_ID_DISPLAY_ONLY_FIELDS', 205);
        define('MOLAJO_EXTENSION_OPTION_ID_PUBLISH_FIELDS', 210);
        define('MOLAJO_EXTENSION_OPTION_ID_JSON_FIELDS', 220);

        /** Status */
        define('MOLAJO_EXTENSION_OPTION_ID_STATUS', 250);

        /** User Interface */
        define('MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_LIST', 300);
        define('MOLAJO_EXTENSION_OPTION_ID_SUBMENU_LIST', 310);
        define('MOLAJO_EXTENSION_OPTION_ID_FILTERS_LIST', 320);
        define('MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_EDIT', 330);
        define('MOLAJO_EXTENSION_OPTION_ID_EDITOR_BUTTONS', 340);

        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO', 400);
        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE', 410);
        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_TEXT', 420);
        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_VIDEO', 430);

        /** Plugin Type */
        define('MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE', 6000);

        /** ACL Component Information */
        define('MOLAJO_EXTENSION_OPTION_ID_ACL_ITEM_TESTS', 10100);
        define('MOLAJO_EXTENSION_OPTION_ID_ACL_TASK_TO_METHODS', 10200);
    }

    /**
     * _installCheck
     *
     * Determine if the site has already been installed
     *
     * return  void
     * @since  1.0
     */
    protected function _installCheck()
    {
        if (defined('MOLAJO_INSTALL_CHECK')) {
        } else {
            define('MOLAJO_INSTALL_CHECK', false);
        }

        if (MOLAJO_APPLICATION == 'installation'
            || (MOLAJO_INSTALL_CHECK === false
                && file_exists(SITE_FOLDER_PATH . '/configuration.php'))
        ) {

        } else {
            if (!file_exists(SITE_FOLDER_PATH . '/configuration.php')
                || filesize(SITE_FOLDER_PATH . '/configuration.php' < 10)
            ) {

                $redirect = MOLAJO_BASE_URL . 'installation/';
                header('Location: ' . $redirect);
                exit();
            }
        }
    }

    protected function _identifySite()
    {

        if (defined('SITES_MEDIA_FOLDER')) {
        } else {
            define('SITES_MEDIA_FOLDER', SITES . '/media');
        }
        if (defined('SITES_MEDIA_URL')) {
        } else {
            define('SITES_MEDIA_URL', MOLAJO_BASE_URL . 'sites/media');
        }
        if (defined('SITES_TEMP_FOLDER')) {
        } else {
            define('SITES_TEMP_FOLDER', SITES . '/temp');
        }
        if (defined('SITES_TEMP_URL')) {
        } else {
            define('SITES_TEMP_URL', MOLAJO_BASE_URL . 'sites/temp');
        }

        $siteBase = substr(MOLAJO_BASE_URL, strlen(MOLAJO_PROTOCOL), 999);
        if (defined('SITE_BASE_URL')) {
        } else {
            $sites = simplexml_load_file(MOLAJO_APPLICATIONS . '/Configuration/sites.xml', 'SimpleXMLElement');
            foreach ($sites->site as $single) {
                if ($single->base == $siteBase) {
                    define('SITE_BASE_URL', $single->base);
                    define('SITE_FOLDER_PATH', $single->folderpath);
                    define('SITE_APPEND_TO_BASE_URL', $single->appendtobaseurl);
                    define('SITE_ID', $single->id);
                    break;
                }
            }
            if (defined('SITE_BASE_URL')) {
            } else {
                echo 'Fatal Error: Cannot identify site for: ' . $siteBase;
                die;
            }
        }
    }

    /**
     * _setPaths
     *
     * Retrieves site configuration information
     * and sets paths for site file locations
     *
     * @results  null
     * @since    1.0
     */
    protected function _setPaths()
    {
        if (defined('SITE_NAME')) {
        } else {
            define('SITE_NAME', Service::Configuration()->get('site_name', SITE_ID));
        }

        if (defined('SITE_CACHE_FOLDER')) {
        } else {
            define('SITE_CACHE_FOLDER', Service::Configuration()->get('cache_path', SITE_FOLDER_PATH . '/cache'));
        }

        if (defined('SITE_LOGS_FOLDER')) {
        } else {
            define('SITE_LOGS_FOLDER', Service::Configuration()->get('logs_path', SITE_FOLDER_PATH . '/logs'));
        }

        /** following must be within the web document folder */
        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', Service::Configuration()->get('media_path', SITE_FOLDER_PATH . '/media'));
        }

        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', MOLAJO_BASE_URL . Service::Configuration()->get('media_url', MOLAJO_BASE_URL . 'sites/' . SITE_ID . '/media'));
        }

        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', Service::Configuration()->get('temp_path', SITE_FOLDER_PATH . '/temp'));
        }
        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', MOLAJO_BASE_URL . Service::Configuration()->get('temp_url', MOLAJO_BASE_URL . 'sites/' . SITE_ID . '/temp'));
        }

        return;
    }

    /**
     *  _identifyApplication
     *
     *  Identify current application and page request
     *
     */
    protected function _identifyApplication()
    {

        /** ex. /molajo/administrator/index.php?option=login    */
        $requestURI = strtolower($_SERVER["REQUEST_URI"]);

        /** remove folder ex. /molajo/                          */
        $requestURI = substr(
            $requestURI,
            strlen(MOLAJO_FOLDER),
            strlen($requestURI) - strlen(MOLAJO_FOLDER)
        );

        /** extract first node for testing as application name  */
        if (strpos($requestURI, '/')) {
            $applicationTest = substr($requestURI, 0, strpos($requestURI, '/'));
        } else {
            $applicationTest = $requestURI;
        }

        $pageRequest = '';
        if (defined('MOLAJO_APPLICATION')) {
            /* must also define MOLAJO_PAGE_REQUEST */
        } else {
            $apps = simplexml_load_file(MOLAJO_APPLICATIONS . '/Configuration/applications.xml', 'SimpleXMLElement');

            foreach ($apps->application as $app) {

                if ($app->name == $applicationTest) {

                    define('MOLAJO_APPLICATION', $app->name);
                    define('MOLAJO_APPLICATION_URL_PATH', MOLAJO_APPLICATION . '/');

                    $pageRequest = substr(
                        $requestURI,
                        strlen(MOLAJO_APPLICATION) + 1,
                        strlen($requestURI) - strlen(MOLAJO_APPLICATION) + 1
                    );
                    break;
                }
            }

            if (defined('MOLAJO_APPLICATION')) {
            } else {
                define('MOLAJO_APPLICATION', $apps->default->name);
                define('MOLAJO_APPLICATION_URL_PATH', '');
                $pageRequest = $requestURI;
            }
        }

        /*  Page Request used in Molajo::Request                */
        if (defined('MOLAJO_PAGE_REQUEST')) {
        } else {
            if (strripos($pageRequest, '/') == (strlen($pageRequest) - 1)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/'));
            }
            define('MOLAJO_PAGE_REQUEST', $pageRequest);
        }
    }
}

