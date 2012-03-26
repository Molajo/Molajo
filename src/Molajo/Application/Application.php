<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Application\MVC\Model\SitesModel;

defined('MOLAJO') or die;

/**
 * Application
 *
 * @package   Molajo
 * @subpackage  Application
 * @since        1.0
 */
Class Application
{
    /**
     * $instance
     *
     * @var        object
     * @since      1.0
     */
    protected static $instance = null;

    /**
     * $config
     *
     * @var        integer
     * @since      1.0
     */
    protected $site_config = null;

    /**
     * $site_parameters
     *
     * @var        array
     * @since      1.0
     */
    protected $site_parameters = null;

    /**
     * $site_custom_fields
     *
     * @var        array
     * @since      1.0
     */
    protected $site_custom_fields = null;

    /**
     * $rendered_output
     *
     * @var        string
     * @since      1.0
     */
    protected $rendered_output = null;

    /**
     * getInstance
     *
     * Returns the global site object, creating if not existing
     *
     * @return  Application  object
     * @since   1.0
     */
    public static function getInstance()
    {
        if (self::$instance) {
        } else {
            self::$instance = new Application();
        }
        return self::$instance;
    }

    /**
     * initialise
     *
     * Retrieves the configuration information,
     * loads language files, editor, triggers onAfterInitialise
     *
     * @param    array
     *
     * @return null
     * @since 1.0
     */
    public function initialise()
    {
        if (version_compare(PHP_VERSION, '5.3', '<')) {
            die('Your host needs to use PHP 5.3 or higher to run Molajo.');
        }

        /** HTTP Class */
        $this->_setBaseURL();

        /** PHP Constants */
        $this->_setDefines();

        /** Site determination and paths */
        $this->_setSite();

        /** Application determination and paths */
        $this->_setApplication();

        /** Connect Application Services */
        Molajo::Services()
            ->startServices();

        Services::Debug()
            ->set('Molajo::Services()->startServices() complete');

        if (Services::Registry()->get('Configuration\\force_ssl') > 0) {
            if ((Services::Request()->isSecure() === true)) {
            } else {

                $redirectTo = (string)'https' .
                    substr(MOLAJO_BASE_URL, 4, strlen(MOLAJO_BASE_URL) - 4) .
                    MOLAJO_APPLICATION_URL_PATH .
                    '/' . MOLAJO_PAGE_REQUEST;

                return Services::Redirect()
                    ->set($redirectTo, 301);
            }
        }

        /** establish the session */
        //Services::Session()->create(
        //        Services::Session()->getHash(get_class($this))
        //  );

        Services::Debug()
            ->set('Services::Session()->create complete');

        /** Site Paths, Custom Fields, and Authorisation */
        $this->_setSitePaths();

        $m = new SitesModel ();
        $m->query->where($m->db->qn('id') . ' = ' . (int)SITE_ID);
        $info = $m->loadObject();
        if ($info === false) {
            //error! die!
        }

        $authorise = Services::Access()
            ->authoriseSiteApplication();
        if ($authorise === false) {
            $message = '304: ' . MOLAJO_BASE_URL;
            echo $message;
            die;
        }

        $this->site_custom_fields = Services::Registry()->initialise();
        $this->site_custom_fields->loadString($info->custom_fields);

        $this->site_parameters = Services::Registry()->initialise();
        $this->site_parameters->loadString($info->parameters);

        $this->site_metadata = Services::Registry()->initialise();
        $this->site_metadata->loadString($info->metadata);

        $this->base_url = $info->base_url;

        Services::Debug()
            ->set('Molajo::Application()->initialise() complete');

        return $this;
    }

    /**
     * request
     *
     * @param null $override_request_url
     * @param null $override_asset_id
     *
     * @return Application
     * @since  1.0
     */
    public function request($override_request_url = null,
                            $override_asset_id = null)
    {
        Molajo::Request()->process(
            $override_request_url = null,
            $override_asset_id = null
        );

        return $this;
    }

    /**
     * process
     *
     * Executes a display or action task
     *
     * Display Task
     *
     * 1. Parse: recursively parses theme and then rendered output
     *      for <include:type statements
     *
     * 2. Includer: each include statement is processed by the
     *      associated extension includer in order, collecting
     *      rendering data needed by the MVC
     *
     * 3. MVC: executes controller task, invoking model processing and
     *    rendering of template and wrap views
     *
     * Steps 1-3 continue until no more <include:type statements are
     *    found in the Theme and rendered output
     *
     * Action Task
     *
     * @param string $override_sequenceXML
     * @param string $override_finalXML
     * @return Application
     */
    public function process($override_sequenceXML = null, $override_finalXML = null)
    {
        if (Services::Redirect()->url === null
            && (int)Services::Redirect()->code == 0
        ) {
        } else {
            return $this;
        }

        if (Services::Registry()->get('Request\\mvc_controller') == 'display') {
            $this->rendered_output =
                Molajo::Parse()
                    ->process($override_sequenceXML = null, $override_finalXML = null);
            Services::Debug()
                ->set('Molajo::Parse() complete');

        } else {

            /**
             * Action Task
             */
            //$this->_processTask();
        }

        Services::Debug()
            ->set('Molajo::Application()->process() Complete');
        return $this;
    }

    /**
     * response
     *
     * @return mixed
     */
    public function response()
    {
        if (Services::Redirect()->url === null
            && (int)Services::Redirect()->code == 0) {

            Services::Debug()
                ->set('Services::Response()->setContent() for ' . $this->rendered_output . ' Code: 200');

            Services::Response()
                ->setContent($this->rendered_output)
                ->setStatusCode(200)
                ->prepare(Services::Request()->request)
                ->send();

        } else {

            Services::Debug()
                ->set('Services::Redirect()->redirect()->send() for ' . Services::Redirect()->url . ' Code: ' . Services::Redirect()->code);

            Services::Redirect()
                ->redirect()
                ->send();
        }

        Services::Debug()
            ->set('Molajo::Application()->response End');

        exit(0);
    }

    /**
     * _setBaseURL
     *
     * Class constructor.
     *
     * @return  void
     * @since   1.0
     */
    protected function _setBaseURL()
    {
        $baseURL = Molajo::RequestService()->request->getScheme()
            . '://'
            . Molajo::RequestService()->request->getHttpHost()
            . Molajo::RequestService()->request->getBaseUrl();

        if (defined('MOLAJO_BASE_URL')) {
        } else {
            define('MOLAJO_BASE_URL', $baseURL . '/');
        }
        return;
    }

    /**
     *  _setDefines
     *
     * The MOLAJO_APPLICATIONS, MOLAJO_EXTENSIONS and VENDOR
     * folders and subfolders can be relocated outside of the
     * Apache htdocs folder for increased security. To do so:
     *
     * - create a defines.php file placed in the root of this site
     * that defines the location of those files (except VENDOR)
     *
     * - create an autoloadoverride.php file to replace the
     * Molajo/Common/Autoload.php file defining the namespaces
     *
     * SITES contains content that must be accessible by the
     * Website and thus cannot be moved
     */
    protected function _setDefines()
    {
        if (file_exists(MOLAJO_BASE_FOLDER . '/defines.php')) {
            include_once MOLAJO_BASE_FOLDER . '/defines.php';
        }

        if (defined('MOLAJO_EXTENSIONS')) {
        } else {
            define('MOLAJO_EXTENSIONS', MOLAJO_BASE_FOLDER . '/Molajo/Extension');
        }
        if (defined('SITES')) {
        } else {
            define('SITES', MOLAJO_BASE_FOLDER . '/site');
        }
        if (defined('MOLAJO_CONFIGURATION_FOLDER')) {
        } else {
            define('MOLAJO_CONFIGURATION_FOLDER', MOLAJO_BASE_FOLDER . '/Molajo/Application/Configuration');
        }

        /** Define PHP constants for application variables */
        $defines = simplexml_load_file(MOLAJO_CONFIGURATION_FOLDER. '/defines.xml');

        foreach ($defines->define as $item) {
            if (defined((string)$item['name'])) {
            } else {
                $value = (string)$item['value'];
                define((string)$item['name'], $value);
            }
        }

        /**
         *  Applications
         */
        if (defined('MOLAJO_APPLICATIONS_MVC')) {
        } else {
            define('MOLAJO_APPLICATIONS_MVC', MOLAJO_APPLICATIONS . '/MVC');
        }
        if (defined('MOLAJO_APPLICATIONS_MVC_URL')) {
        } else {
            define('MOLAJO_APPLICATIONS_MVC_URL', MOLAJO_BASE_URL . 'Molajo/Application/MVC');
        }

        /**
         *  Extensions
         */
        if (defined('MOLAJO_EXTENSIONS_COMPONENTS')) {
        } else {
            define('MOLAJO_EXTENSIONS_COMPONENTS', MOLAJO_EXTENSIONS . '/Component');
        }
        if (defined('MOLAJO_EXTENSIONS_FORMFIELDS')) {
        } else {
            define('MOLAJO_EXTENSIONS_FORMFIELDS', MOLAJO_EXTENSIONS . '/Formfield');
        }
        if (defined('MOLAJO_EXTENSIONS_LANGUAGES')) {
        } else {
            define('MOLAJO_EXTENSIONS_LANGUAGES', MOLAJO_EXTENSIONS . '/Language');
        }
        if (defined('MOLAJO_EXTENSIONS_MODULES')) {
        } else {
            define('MOLAJO_EXTENSIONS_MODULES', MOLAJO_EXTENSIONS . '/Module');
        }
        if (defined('MOLAJO_EXTENSIONS_PLUGINS')) {
        } else {
            define('MOLAJO_EXTENSIONS_PLUGINS', MOLAJO_EXTENSIONS . '/Plugin');
        }
        if (defined('MOLAJO_EXTENSIONS_THEMES')) {
        } else {
            define('MOLAJO_EXTENSIONS_THEMES', MOLAJO_EXTENSIONS . '/Theme');
        }
        if (defined('MOLAJO_EXTENSIONS_VIEWS')) {
        } else {
            define('MOLAJO_EXTENSIONS_VIEWS', MOLAJO_EXTENSIONS . '/View');
        }

        if (defined('MOLAJO_EXTENSIONS_COMPONENTS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_COMPONENTS_URL', MOLAJO_BASE_URL . 'Molajo/Extension/Component');
        }
        if (defined('MOLAJO_EXTENSIONS_FORMFIELDS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_FORMFIELDS_URL', MOLAJO_BASE_URL . 'Molajo/Extension/Formfield');
        }
        if (defined('MOLAJO_EXTENSIONS_MODULES_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_MODULES_URL', MOLAJO_BASE_URL . 'Molajo/Extension/Module');
        }
        if (defined('MOLAJO_EXTENSIONS_PLUGINS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_PLUGINS_URL', MOLAJO_BASE_URL . 'Molajo/Extension/Plugin');
        }
        if (defined('MOLAJO_EXTENSIONS_THEMES_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_THEMES_URL', MOLAJO_BASE_URL . 'Molajo/Extension/Theme');
        }
        if (defined('MOLAJO_EXTENSIONS_VIEWS_URL')) {
        } else {
            define('MOLAJO_EXTENSIONS_VIEWS_URL', MOLAJO_BASE_URL . 'Molajo/Extension/View');
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
         *  TO BE REMOVED
         */
        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO', 400);
        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE', 410);
        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_TEXT', 420);
        define('MOLAJO_EXTENSION_OPTION_ID_MIMES_VIDEO', 430);

        return;
    }

    /**
     * _setSite
     *
     * Identifies the specific site and sets site paths
     * for use in the application
     *
     * @return  void
     * @since   1.0
     */
    protected function _setSite()
    {
        if (defined('SITES')) {
        } else {
            define('SITES', MOLAJO_BASE_FOLDER . '/Site');
        }
        if (defined('SITES_MEDIA_FOLDER')) {
        } else {
            define('SITES_MEDIA_FOLDER', SITES . '/media');
        }
        if (defined('SITES_MEDIA_URL')) {
        } else {
            define('SITES_MEDIA_URL', MOLAJO_BASE_URL . 'site/media');
        }
        if (defined('SITES_TEMP_FOLDER')) {
        } else {
            define('SITES_TEMP_FOLDER', SITES . '/temp');
        }
        if (defined('SITES_TEMP_URL')) {
        } else {
            define('SITES_TEMP_URL', MOLAJO_BASE_URL . 'site/temp');
        }

        $scheme = Molajo::RequestService()
            ->request->getScheme() . '://';
        $siteBase = substr(MOLAJO_BASE_URL, strlen($scheme), 999);

        if (defined('SITE_BASE_URL')) {
        } else {
            $sites = simplexml_load_file(MOLAJO_CONFIGURATION_FOLDER. '/sites.xml');

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
     *  _setApplication
     *
     *  Identify current application and page request
     *
     * @return  void
     * @since   1.0
     */
    protected function _setApplication()
    {
        /** ex. /molajo/administrator/index.php?option=login    */
        $p1 = Molajo::RequestService()->request->getPathInfo();
        $t2 = Molajo::RequestService()->request->getQueryString();
        if (trim($t2) == '') {
            $requestURI = $p1;
        } else {
            $requestURI = $p1 . '?' . $t2;
        }

        /** remove the first /  */
        $requestURI = substr($requestURI, 1, 9999);

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
            $apps = simplexml_load_file(MOLAJO_CONFIGURATION_FOLDER. '/applications.xml');

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
                //todo: use HTTPFoundation redirect
                $redirect = MOLAJO_BASE_URL . 'installation/';
                header('Location: ' . $redirect);
                exit();
            }
        }
    }

    /**
     * _setSitePaths
     *
     * Establish media, cache, log, etc., locations for site for application use
     *
     * @return mixed
     * @since  1.0
     */
    protected function _setSitePaths()
    {
        if (defined('SITE_NAME')) {
        } else {
            define('SITE_NAME', Services::Registry()->get('Configuration\\site_name', SITE_ID));
        }

        if (defined('SITE_CACHE_FOLDER')) {
        } else {
            define('SITE_CACHE_FOLDER', Services::Registry()->get('Configuration\\cache_path', SITE_FOLDER_PATH . '/cache'));
        }

        if (defined('SITE_LOGS_FOLDER')) {
        } else {
            define('SITE_LOGS_FOLDER', Services::Registry()->get('Configuration\\logs_path', SITE_FOLDER_PATH . '/logs'));
        }

        /** following must be within the web document folder */
        if (defined('SITE_MEDIA_FOLDER')) {
        } else {
            define('SITE_MEDIA_FOLDER', Services::Registry()->get('Configuration\\media_path', SITE_FOLDER_PATH . '/media'));
        }

        if (defined('SITE_MEDIA_URL')) {
        } else {
            define('SITE_MEDIA_URL', MOLAJO_BASE_URL . Services::Registry()->get('Configuration\\media_url', MOLAJO_BASE_URL . 'sites/' . SITE_ID . '/media'));
        }

        if (defined('SITE_TEMP_FOLDER')) {
        } else {
            define('SITE_TEMP_FOLDER', Services::Registry()->get('Configuration\\temp_path', SITE_FOLDER_PATH . '/temp'));
        }
        if (defined('SITE_TEMP_URL')) {
        } else {
            define('SITE_TEMP_URL', MOLAJO_BASE_URL . Services::Registry()->get('Configuration\\temp_url', MOLAJO_BASE_URL . 'sites/' . SITE_ID . '/temp'));
        }
        return;
    }
}
