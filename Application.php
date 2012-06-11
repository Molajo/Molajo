<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo;

use Molajo\Service\Services\Request\RequestService;
use Molajo\Service\Services\Configuration\ConfigurationService;
use Molajo\Extension\Helpers;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Application
 *
 * @package     Molajo
 * @subpackage  Application
 * @since       1.0
 */
Class Application
{
    /**
     * Application::Services
     *
     * @var    object Services
     * @since  1.0
     */
    protected static $services = null;

    /**
     * Application::Helpers
     *
     * @var    object Helper
     * @since  1.0
     */
    protected static $helpers = null;

    /**
     * Application::Request
     *
     * @var    object Request
     * @since  1.0
     */
    protected static $request = null;

    /**
     * $rendered_output
     *
     * @var        string
     * @since      1.0
     */
    protected $rendered_output = null;

    /**
     * Application Controller
     *
     * Override normal processing with these parameters
     *
     * @param string $override_url_request
     * @param string $override_catalog_id
     * @param string $override_sequence_xml
     * @param string $override_final_xml
     *
     *  1. Initialise
     *  2. Route
     *  3. Authorise
     *  3. Execute (Display or Action)
     *  4. Response
     *
     * @return mixed
     * @since   1.0
     */
    public function process($override_url_request = false, $override_catalog_id = false,
                            $override_sequence_xml = false, $override_final_xml = false)
    {
        /** Initialise Sets the Configuration Registry  */
        $continue = $this->initialise();

        Services::Registry()->set('Override', 'url_request', $override_url_request);
        Services::Registry()->set('Override', 'catalog_id', $override_catalog_id);
        Services::Registry()->set('Override', 'sequence_xml', $override_sequence_xml);
        Services::Registry()->set('Override', 'final_xml', $override_final_xml);

        if ($continue == false) {
            Services::Debug()->set('Application Initialise failed');

            return;
        } else {
            Services::Debug()->set('Application Initialise succeeded');
        }

        /** Route: Sets the Request, Catalog, and Menuitem Registry */
        $continue = $this->route();

        if ($continue == false) {
            Services::Debug()->set('Application Route failed');
            return;
        } else {
            Services::Debug()->set('Application Route succeeded');
        }

        /** Authorise: Services::Registry()->get('Parameters', 'status_authorised') */
        $continue = $this->authorise();

        if ($continue === false) {
            Services::Debug()->set('Application Authorise failed');
            return;
        } else {
            Services::Debug()->set('Application Authorise succeeded');
        }

        /** Execute */
        $continue = $this->execute();

        if ($continue == false) {
            Services::Debug()->set('Application Execute failed');
            return;
        } else {
            Services::Debug()->set('Application Execute succeeded');
        }

        /** Response */
        $continue = $this->response();

        if ($continue == false) {
            Services::Debug()->set('Application Response failed');
            return;
        } else {
            Services::Debug()->set('Application Response succeeded');
        }

        return;
    }

    /**
     * Initialise Site, Application, and Services
     *
     * @return boolean
     * @since   1.0
     */
    protected function initialise()
    {
        if (version_compare(PHP_VERSION, '5.3', '<')) {
            die('Your host needs to use PHP 5.3 or higher to run Molajo.');
        }

        /** HTTP class */
        $continue = $this->setBaseURL();
        if ($continue == false) {
            return false;
        }

        /** PHP constants */
        $continue = $this->setDefines();
        if ($continue == false) {
            return false;
        }

        /** Site determination and paths */
        $continue = $this->setSite();
        if ($continue == false) {
            return false;
        }

        /** Application determination and paths */
        $continue = $this->setApplication();
        if ($continue == false) {
            return false;
        }

        /** Application installation check */
        $continue = $this->installCheck();
        if ($continue == false) {
            return false;
        }

        /** Connect Application Services */
        $continue = Application::Services()->StartServices();
        if ($continue == false) {
            return false;
        }

        /** SSL Check */
        $continue = $this->sslCheck();
        if ($continue == false) {
            return false;
        }

        /** Verify that this site is authorised to access this application */
        $continue = $this->getSiteApplicationAuthorisation();
        if ($continue == false) {
            return false;
        }

		/** Connect Helpers */
		$continue = Application::Helpers()->connect();
		if ($continue == false) {
			return false;
		}

		/** Session */
        //Services::Session()->create(
        //        Services::Session()->getHash(get_class($this))
        //  );
        // Services::Debug()
        // ->set('Services::Session()->create complete');

        return true;
    }

    /**
     * Evaluates HTTP Request to determine routing requirements, including:
     *
     * - Normal page request: populates Registry for Request, Catalog, and Menuitem (if appropriate)
     *     Saves array of request_non_routable_parameters (if identified in request) to Request registry
     * - Issues redirect request for "home" duplicate content
     * - For 'Application Offline Mode', sets a 503 error and registry values for View
     * - For 'Page not found', sets 404 error and registry values for Error Template/View
     * - For defined redirect with Catalog, issues 301 Redirect to new URL
     * - For 'Logon requirement' situations, issues 303 redirect to configured login page
     *
     * @return boolean
     *
     * @since  1.0
     */
    protected function route()
    {
        $results = Services::Route()->process();

        if ($results == false) {
            return false;

        } elseif (Services::Redirect()->url === null && (int) Services::Redirect()->code == 0) {
	        return true;
        }

		return false;
    }

    /**
     * Verify user authorization
     *
     * @return boolean
     * @since    1.0
     */
    protected function authorise()
    {
        return Services::Authorisation()->authoriseAction();
    }

    /**
     * execute the action requested
     *
     * @return boolean
     * @since  1.0
     */
    protected function execute()
    {
        $action = Services::Registry()->get('Parameters', 'request_action', 'display');

        /** Display Action */
        if ($action == 'display') {

            $continue = $this->display();

            if ($continue == false) {
                Services::Debug()->set('Application execute Display failed');
                return false;

            } else {
                Services::Debug()->set('Application execute Display succeeded');
                return true;
            }
        }

        /** Non-Display Actions */
        $continue = $this->action();

        if ($continue == false) {
            Services::Debug()->set('Application execute ' . $action . ' failed');
            return false;

        } else {
            Services::Debug()->set('Application execute ' . $action . ' succeeded');
            return true;
        }
    }

    /**
     * Executes a display action
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
     * 3. MVC: executes controller action, invoking model processing and
     *    rendering of template and wrap views
     *
     * Steps 1-3 continue until no more <include:type statements are
     *    found in the Theme and rendered output
     *
     * @param string $override_sequenceXML
     * @param string $override_finalXML
     *
     * @return Application
     */
    protected function display()
    {
		//Services::Registry()->get('Parameters', '*');
		//Services::Message()->set('Test message', MESSAGE_TYPE_WARNING, 111);
		//Services::Message()->set('Test message', MESSAGE_TYPE_ERROR, 999);

		/** Theme and Page must exist */
		if (file_exists(Services::Registry()->get('Parameters', 'theme_path_include'))
			&& file_exists(Services::Registry()->get('Parameters', 'page_view_path_include'))
		) {
		} else {
			Services::Error()->set(500, 'Theme and/or Page View Not found');
			echo 'Theme and/or Page View Not found - application stopped before parse. Parameters follow:';
			Services::Registry()->get('Parameters', '*');
			die;
		}

		/** Save Route Parameters */
		Services::Registry()->copy('Parameters', 'RouteParameters');

		$this->rendered_output = Services::Parse()->process();

        return $this;
    }

    /**
     * Execute action (other than Display)
     *
     * @return boolean
     */
    protected function action()
    {
        /** Action: Database action
        $temp = Services::Registry()->initialise();
        $temp->loadArray($this->parameters);
        $this->parameters = $temp;

        if (Services::Registry()->get('Configuration', 'url_sef', 1) == 0) {
            $link = $this->page_request->get('url_request_sef');
        } else {
            $link = $this->page_request->get('url_request');
        }
        Services::Registry()->set('Request', 'redirect_on_failure', $link);

        Services::Registry()->set('Request', 'model',
            ucfirst(trim(Services::Registry()->get('Request', 'mvc_model'))) . 'Model');
        $cc = 'Molajo' . ucfirst(Services::Registry()->get('Request', 'mvc_controller')) . 'Controller';
        Services::Registry()->set('Request', 'controller', $cc);
        $action = Services::Registry()->get('Parameters', 'request_action');
        Services::Registry()->set('Parameters', 'request_action', $action);
        Services::Registry()->set('Request', 'id', Services::Registry()->get('Request', 'mvc_id'));
        $controller = new $cc($this->page_request, $this->parameters);
        */
        /** execute action: non-display, edit, or add action
        $continue = $controller->$action();
         */
        //redirect

        return true;
    }

    /**
     * Return HTTP response
     *
     * @return object
     * @since  1.0
     */
    protected function response()
    {
        if (Services::Redirect()->url === null
            && (int) Services::Redirect()->code == 0
        ) {

            Services::Debug()
                ->set('Services::Response()->setContent() for ' . $this->rendered_output . ' Code: 200');

            Services::Response()
                ->setContent($this->rendered_output)
                ->setStatusCode(200)
                ->prepare(Services::Request()->get('request'))
                ->send();

        } else {

            Services::Debug()
                ->set('Services::Redirect()->redirect()->send() for '
                . Services::Redirect()->url . ' Code: ' . Services::Redirect()->code);

            Services::Redirect()
                ->redirect()
                ->send();
        }

        Services::Debug()
            ->set('Application response End');

        exit(0);
    }

    /**
     * Populate BASE_URL using scheme, host, and base URL
     *
     * Note: The Application::Request object is used instead of the Services::Request due to where
     * processing is at this point
     *
     * @return boolean
     * @since   1.0
     */
    protected function setBaseURL()
    {
        $baseURL = Application::Request()->get('request')->getScheme()
            . '://'
            . Application::Request()->get('request')->getHttpHost()
            . Application::Request()->get('request')->getBaseUrl();

        if (defined('BASE_URL')) {
        } else {
            /**
             * BASE_URL - root of the website with a trailing slash
             */
            define('BASE_URL', $baseURL . '/');
        }

        return true;
    }

    /**
     * The APPLICATIONS, EXTENSIONS and VENDOR
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
     *
     * @return boolean
     * @since   1.0
     */
    protected function setDefines()
    {

//todo: now that namespaces are used, how much of this is really needed?

        /** Override Hint */
        if (file_exists(BASE_FOLDER . '/defines.php')) {
            /** @noinspection PhpIncludeInspection */
            include_once BASE_FOLDER . '/defines.php';
        }

        if (defined('CONFIGURATION_FOLDER')) {
        } else {
            define('CONFIGURATION_FOLDER', BASE_FOLDER . '/Molajo/Configuration');
        }

        if (defined('EXTENSIONS')) {
        } else {
            define('EXTENSIONS', BASE_FOLDER . '/Molajo/Extension');
        }
        if (defined('EXTENSIONS_HELPERS')) {
        } else {
            define('EXTENSIONS_HELPERS', EXTENSIONS . '/Helper');
        }
        if (defined('EXTENSIONS_COMPONENTS')) {
        } else {
            define('EXTENSIONS_COMPONENTS', EXTENSIONS . '/Component');
        }
        if (defined('EXTENSIONS_FORMFIELDS')) {
        } else {
            define('EXTENSIONS_FORMFIELDS', EXTENSIONS . '/Formfield');
        }
        if (defined('EXTENSIONS_MODULES')) {
        } else {
            define('EXTENSIONS_MODULES', EXTENSIONS . '/Module');
        }
        if (defined('EXTENSIONS_THEMES')) {
        } else {
            define('EXTENSIONS_THEMES', EXTENSIONS . '/Theme');
        }
        if (defined('EXTENSIONS_TRIGGERS')) {
        } else {
            define('EXTENSIONS_TRIGGERS', EXTENSIONS . '/Trigger');
        }
        if (defined('EXTENSIONS_VIEWS')) {
        } else {
            define('EXTENSIONS_VIEWS', EXTENSIONS . '/View');
        }
		if (defined('EXTENSIONS_LANGUAGES')) {
		} else {
			define('EXTENSIONS_LANGUAGES', EXTENSIONS . '/Language');
		}

		if (defined('EXTENSIONS_URL')) {
        } else {
            define('EXTENSIONS_URL', BASE_URL . 'Molajo/Extension');
        }
        if (defined('EXTENSIONS_COMPONENTS_URL')) {
        } else {
            define('EXTENSIONS_COMPONENTS_URL', BASE_URL . 'Molajo/Extension/Component');
        }
        if (defined('EXTENSIONS_FORMFIELDS_URL')) {
        } else {
            define('EXTENSIONS_FORMFIELDS_URL', BASE_URL . 'Molajo/Extension/Formfield');
        }

		if (defined('EXTENSIONS_MODULES_URL')) {
        } else {
            define('EXTENSIONS_MODULES_URL', BASE_URL . 'Molajo/Extension/Module');
        }
        if (defined('EXTENSIONS_THEMES_URL')) {
        } else {
            define('EXTENSIONS_THEMES_URL', BASE_URL . 'Molajo/Extension/Theme');
        }
        if (defined('EXTENSIONS_TRIGGERS_URL')) {
        } else {
            define('EXTENSIONS_TRIGGERS_URL', BASE_URL . 'Molajo/Extension/Trigger');
        }
        if (defined('EXTENSIONS_VIEWS_URL')) {
        } else {
            define('EXTENSIONS_VIEWS_URL', BASE_URL . 'Molajo/Extension/View');
        }

        if (defined('MVC')) {
        } else {
            define('MVC', APPLICATIONS . '/MVC');
        }
        if (defined('MVC_URL')) {
        } else {
            define('MVC_URL', BASE_URL . 'Molajo/MVC');
        }

        if (defined('SERVICES')) {
        } else {
            define('SERVICES', APPLICATIONS . '/Service');
        }

        if (defined('SITES')) {
        } else {
            define('SITES', BASE_FOLDER . '/site');
        }

        /**
         *  Allows for quoting in language .ini files.
         */
        if (defined('LANGUAGE_QUOTE_REPLACEMENT')) {
        } else {
            define('LANGUAGE_QUOTE_REPLACEMENT', '"');
        }

        /** Define PHP constants for application variables */
        $defines = ConfigurationService::getFile('Application', 'Defines');
        foreach ($defines->define as $item) {
            if (defined((string) $item['name'])) {
            } else {
                $value = (string) $item['value'];
                define((string) $item['name'], $value);
            }
        }

        /**
         *  EXTENSION OPTIONS
         *
         *  TO BE REMOVED
         */
        define('EXTENSION_OPTION_ID_MIMES_AUDIO', 400);
        define('EXTENSION_OPTION_ID_MIMES_IMAGE', 410);
        define('EXTENSION_OPTION_ID_MIMES_TEXT', 420);
        define('EXTENSION_OPTION_ID_MIMES_VIDEO', 430);

        return true;
    }

    /**
     * Identifies the specific site and sets site paths
     * for use in the application
     *
     * @return boolean
     * @since   1.0
     */
    protected function setSite()
    {
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
            define('SITES_MEDIA_URL', BASE_URL . 'site/media');
        }
        if (defined('SITES_TEMP_FOLDER')) {
        } else {
            define('SITES_TEMP_FOLDER', SITES . '/temp');
        }
        if (defined('SITES_TEMP_URL')) {
        } else {
            define('SITES_TEMP_URL', BASE_URL . 'site/temp');
        }

        /** Note: The Application::Request object is used due to where processing is at this point */
        $scheme = Application::Request()->get('request')->getScheme() . '://';
        $siteBase = substr(BASE_URL, strlen($scheme), strlen(BASE_URL) - strlen($scheme));

        if (defined('SITE_BASE_URL')) {
        } else {

            $sites = ConfigurationService::getFile('Application', 'Sites');

            foreach ($sites->site as $single) {
                if (strtolower($single->base) == strtolower($siteBase)) {
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

        return true;
    }

    /**
     * Identify current application and page request
     *
     * Note: The Application::Request object is used due to where processing is at this point
     *
     * @return boolean
     * @since   1.0
     */
    protected function setApplication()
    {
        /** ex. /molajo/administrator/index.php?option=login    */
        $p1 = Application::Request()->get('request')->getPathInfo();
        $t2 = Application::Request()->get('request')->getQueryString();

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

        if (defined('APPLICATION')) {
            /* to override - must also define PAGE_REQUEST */
        } else {

            $apps = ConfigurationService::getFile('Application', 'Applications');

            foreach ($apps->application as $app) {

                if ($app->name == $applicationTest) {

                    define('APPLICATION', $app->name);
                    define('APPLICATION_URL_PATH', APPLICATION . '/');

                    $pageRequest = substr(
                        $requestURI,
                        strlen(APPLICATION) + 1,
                        strlen($requestURI) - strlen(APPLICATION) + 1
                    );
                    break;
                }
            }

            if (defined('APPLICATION')) {
            } else {
                define('APPLICATION', $apps->default->name);
                define('APPLICATION_URL_PATH', '');
                $pageRequest = $requestURI;
            }
        }

        /*  Page Request used in Application::Request                */
        if (defined('PAGE_REQUEST')) {
        } else {
            if (strripos($pageRequest, '/') == (strlen($pageRequest) - 1)) {
                $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/'));
            }
            define('PAGE_REQUEST', $pageRequest);
        }

        return true;
    }

    /**
     * Determine if the site has already been installed
     *
     * return  boolean
     * @since  1.0
     */
    protected function installCheck()
    {
        if (defined('SKIP_INSTALL_CHECK')) {
            return true;
        }

        if (APPLICATION == 'installation') {
            return true;
        }

        if (file_exists(SITE_FOLDER_PATH . '/configuration.php')
            && filesize(SITE_FOLDER_PATH . '/configuration.php') > 10
        ) {
            return true;
        }

        /** Redirect to Installation Application */
        $redirect = BASE_URL . 'installation/';
        header('Location: ' . $redirect);

        exit();
    }

    /**
     * Check to see if the secure access to the application is required
     *
     * @return bool
     * @since   1.0
     */
    protected function sslCheck()
    {
        /** @noinspection PhpUndefinedMethodInspection */
        Services::Registry()->get('ApplicationsParameters');

        /** @noinspection PhpUndefinedMethodInspection */
        if ((int) Services::Registry()->get('Configuration', 'url_force_ssl', 0) > 0) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ((Services::Request()->get('connection')->isSecure() === true)) {

            } else {

                $redirectTo = (string) 'https' .
                    substr(BASE_URL, 4, strlen(BASE_URL) - 4) .
                    APPLICATION_URL_PATH .
                    '/' . PAGE_REQUEST;

                Services::Redirect()
                    ->set($redirectTo, 301);

                return false;
            }
        }

        return true;
    }

    /**
     * Verify that this site is authorised to access this application
     *
     * @returns boolean
     * @since   1.0
     */
    protected function getSiteApplicationAuthorisation()
    {
        $authorise = Services::Authorisation()->authoriseSiteApplication();
        if ($authorise === false) {
            $message = '304: ' . BASE_URL;
            echo $message;
            die;
        }

        return true;
    }

    /**
     * Application::Services
     *
     * @static
     * @return Services
     * @throws \RuntimeException
     * @since   1.0
     */
    public static function Services()
    {
        if (self::$services) {
        } else {
            try {
                self::$services = Services::getInstance();
            } catch (\RuntimeException $e) {
                echo 'Instantiate Service Exception : ', $e->getMessage(), "\n";
                die;
            }
        }

        return self::$services;
    }

    /**
     * Application::Helpers
     *
     * @static
     * @return Helpers
     * @throws \RuntimeException
     * @since   1.0
     */
    public static function Helpers()
    {
        if (self::$helpers) {
        } else {
            try {
                self::$helpers = Helpers::getInstance();
            } catch (\Exception $e) {
                echo 'Instantiate Helpers Exception : ', $e->getMessage(), "\n";
                die;
            }
        }

        return self::$helpers;
    }

    /**
     * Application::RequestService
     *
     * @static
     * @return RequestService
     * @since   1.0
     */
    public static function Request()
    {
        if (self::$request) {
        } else {
            try {
                self::$request = RequestService::getInstance();
            } catch (\Exception $e) {
                echo 'Instantiate RequestService Exception : ', $e->getMessage(), "\n";
                die;
            }
        }

        return self::$request;
    }
}
