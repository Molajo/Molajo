<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
	 *  4. Respond
	 *
	 * @return mixed
	 * @since   1.0
	 */
	public function process($override_url_request = false, $override_catalog_id = false,
							$override_sequence_xml = false, $override_final_xml = false)
	{
		/** 1. Initialise */
		$results = $this->initialise($override_url_request, $override_catalog_id,
			$override_sequence_xml, $override_final_xml);

		/** 2. Route */
		if ($results == true) {
			$results = $this->route();
		}

		/** 3. Authorise */
		if ($results == true) {
			$results = $this->authorise();
		}

		/** 4. Execute */
		if ($results == true) {
			$results = $this->execute();
		}

		/** 5. Response */
		if ($results == true) {
			$results = $this->response();
		}

		exit(0);
	}

	/**
	 * Initialise Site, Application, and Services
	 *
	 * @param  string $override_url_request
	 * @param  string $override_catalog_id
	 * @param  string $override_sequence_xml
	 * @param  string $override_final_xml
	 *
	 * @return  boolean
	 * @since   1.0
	 */
	protected function initialise($override_url_request = false, $override_catalog_id = false,
								  $override_sequence_xml = false, $override_final_xml = false)

	{
		if (version_compare(PHP_VERSION, '5.3', '<')) {
			die('Your host needs to use PHP 5.3 or higher to run Molajo.');
		}

		/** HTTP class */
		$results = $this->setBaseURL();
		if ($results == false) {
			return false;
		}

		/** PHP constants */
		$results = $this->setDefines();
		if ($results == false) {
			return false;
		}

		/** Site determination and paths */
		$results = $this->setSite();
		if ($results == false) {
			return false;
		}

		/** Application determination and paths */
		$results = $this->setApplication();
		if ($results == false) {
			return false;
		}

		/** Installation check */
		$results = $this->installCheck();
		if ($results == false) {
			return false;
		}

		/** Connect Services */
		$results = Application::Services()->StartServices();
		if ($results == false) {
			return false;
		}

		/** SSL Check */
		$results = $this->sslCheck();
		if ($results == false) {
			return false;
		}

		/** Verify site authorised for application */
		$results = $this->verifySiteApplication();
		if ($results == false) {
			return false;
		}

		/** Connect Helpers */
		$results = Application::Helpers()->connect();
		if ($results == false) {
			return false;
		}

		/** LAZY LOAD Session */
		//Services::Session()->create(
		//        Services::Session()->getHash(get_class($this))
		//  );
		// Services::Debug()
		// ->set('Services::Session()->create complete, 'Application');

		Services::Registry()->set('Override', 'url_request', $override_url_request);
		Services::Registry()->set('Override', 'catalog_id', $override_catalog_id);
		Services::Registry()->set('Override', 'sequence_xml', $override_sequence_xml);
		Services::Registry()->set('Override', 'final_xml', $override_final_xml);

		if ($results == true) {
			Services::Debug()->set('Application Schedule onAfterRoute', LOG_OUTPUT_TRIGGERS);
			$trigger = Services::Event()->schedule('onAfterInitialise');
			if ($trigger['success'] == true) {
				$results = true;
			} else {
				$results = false;
			}
		}

		if ($results == false) {
			Services::Debug()->set('Initialise failed', LOG_OUTPUT_APPLICATION);
			return false;
		}

		Services::Debug()->set('Initialise succeeded', LOG_OUTPUT_APPLICATION);

		return true;
	}

	/**
	 * Evaluates HTTP Request to determine routing requirements, including:
	 *
	 * - Normal page request: populates Registry for Request, Catalog, and Menuitem (if appropriate)
	 *
	 * - Issues redirect request for "home" duplicate content
	 *
	 * - Checks for 'Application Offline Mode', sets a 503 error and registry values for View
	 *
	 * - For 'Page not found', sets 404 error and registry values for Error Template/View
	 *
	 * - For defined redirect with Catalog, issues 301 Redirect to new URL
	 *
	 * - For 'Logon requirement' situations, issues 303 redirect to configured login page
	 *
	 * @return boolean
	 *
	 * @since  1.0
	 */
	protected function route()
	{
//$results = Services::Install()->content();
//$results = Services::Install()->testCreateExtension('Data Dictionary', 'Components');
//$results = Services::Install()->testDeleteExtension('Test', 'Components');

		Services::Debug()->set(START_ROUTING, LOG_OUTPUT_APPLICATION);

		echo 'asfsafds';
		die;
		$results = Services::Route()->process();

		if ($results == true
			&& Services::Redirect()->url === null
			&& (int)Services::Redirect()->code == 0
		) {
			Services::Debug()->set('Application Schedule onAfterRoute', LOG_OUTPUT_TRIGGERS);
			$trigger = Services::Event()->schedule('onAfterRoute');
			if ($trigger['success'] == true) {
				$results = true;
			} else {
				$results = false;
			}
		}

		if ($results == false) {
			Services::Debug()->set('Route failed', LOG_OUTPUT_APPLICATION);
			return false;
		}

		if ($results == true
			&& Services::Redirect()->url === null
			&& (int)Services::Redirect()->code == 0
		) {
			Services::Debug()->set('Route succeeded', LOG_OUTPUT_APPLICATION);
			return true;
		}

		Services::Debug()->set('Route redirected ' . Services::Redirect()->url, LOG_OUTPUT_APPLICATION);

		return true;
	}

	/**
	 * Verify user authorization
	 *
	 * OnAfterAuthorise Event is invoked even when core authorisation fails to authorise
	 * so that authorisation can be overridden and other methods of authorisation can be used
	 *
	 * @return boolean
	 * @since    1.0
	 */
	protected function authorise()
	{
		Services::Debug()->set(START_AUTHORISATION, LOG_OUTPUT_APPLICATION);

		$results = Services::Authorisation()->verifyAction();

		Services::Debug()->set('Application Schedule onAfterAuthorise', LOG_OUTPUT_TRIGGERS);
		$trigger = Services::Event()->schedule('onAfterAuthorise');
		if ($trigger['success'] == true) {
			$results = true;
		} else {
			$results = false;
		}

		if ($results === false) {
			Services::Debug()->set('Authorise failed', LOG_OUTPUT_APPLICATION);
			return false;
		}

		Services::Debug()->set('Authorise succeeded', LOG_OUTPUT_APPLICATION);

		return true;
	}

	/**
	 * Execute the action requested
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function execute()
	{
		Services::Debug()->set(START_EXECUTE, LOG_OUTPUT_APPLICATION);

		$action = Services::Registry()->get('Parameters', 'request_action', 'display');

		/** Display Action */
		if ($action == 'display' || $action == 'edit' || $action == 'add') {
			$results = $this->display();
		} else {
			$results = $this->action();
		}

		if ($results == true) {
			Services::Debug()->set('Application Schedule onAfterExecute', LOG_OUTPUT_TRIGGERS);
			$trigger = Services::Event()->schedule('onAfterExecute');
			if ($trigger['success'] == true) {
				$results = true;
			} else {
				$results = false;
			}
		}

		if ($results == false) {
			Services::Debug()->set('Execute ' . $action . ' failed', LOG_OUTPUT_APPLICATION);
			return false;
		}

		Services::Debug()->set('Execute ' . $action . ' succeeded', LOG_OUTPUT_APPLICATION);

		return true;
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
	 * Override Registry have values for sequence_xml and final_xml
	 *
	 * @since  1.0
	 * @return Application
	 */
	protected function display()
	{
//Services::Registry()->get('Parameters', '*');
//Services::Message()->set('Test message', MESSAGE_TYPE_INFORMATION);
//Services::Message()->set('Test message', MESSAGE_TYPE_SUCCESS);
//Services::Message()->set('Test message', MESSAGE_TYPE_WARNING);
//Services::Message()->set('Test message', MESSAGE_TYPE_ERROR);

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

		/** Save Route Parameters  move to after route */
		Services::Registry()->copy('Parameters', 'RouteParameters');

		$this->rendered_output = Services::Parse()->process();

		return true;
	}

	/**
	 * Execute action (other than Display)
	 *
	 * @return boolean
	 */
	protected function action()
	{

// -> sessions Services::Message()->set('Status updated', MESSAGE_TYPE_SUCCESS);

// 	What action and Controller (authorisation should be okay)

// what redirect for good and bad

// what parameters

		if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
			$url = Services::Registry()->get('Parameters', 'catalog_url_sef_request');

		} else {
			$url = Services::Registry()->get('Parameters', 'catalog_url_request');
		}

		Services::Redirect()->redirect(Services::Url()->getApplicationURL($url), '301')->send();

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
		Services::Debug()->set(START_RESPONSE, LOG_OUTPUT_APPLICATION);

		if (Services::Redirect()->url === null
			&& (int)Services::Redirect()->code == 0
		) {

			Services::Debug()
				->set('Response Code 200', LOG_OUTPUT_APPLICATION);

			$results = Services::Response()
				->setContent($this->rendered_output)
				->setStatusCode(200)
				->prepare(Services::Request()->get('request'))
				->send();

		} else {

			Services::Debug()
				->set('Response Code:' . Services::Redirect()->code
				. 'Redirect to: ' . Services::Redirect()->url
				. LOG_OUTPUT_APPLICATION);

			$results = Services::Redirect()->redirect()->send();
		}

		if ($results == true) {
			Services::Debug()->set('Application Schedule onAfterResponse', LOG_OUTPUT_TRIGGERS);
			$trigger = Services::Event()->schedule('onAfterResponse');
			if ($trigger['success'] == true) {
				$results = true;
			} else {
				$results = false;
			}
		}

		Services::Debug()
			->set('Response exit '. (int) $results, LOG_OUTPUT_APPLICATION);

		return true;
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
		if (defined('EXTENSIONS_COMPONENTS')) {
		} else {
			define('EXTENSIONS_COMPONENTS', EXTENSIONS . '/Component');
		}
		if (defined('EXTENSIONS_LANGUAGES')) {
		} else {
			define('EXTENSIONS_LANGUAGES', EXTENSIONS . '/Language');
		}
		if (defined('EXTENSIONS_HELPERS')) {
		} else {
			define('EXTENSIONS_HELPERS', EXTENSIONS . '/Helper');
		}
		if (defined('EXTENSIONS_MENUITEMS')) {
		} else {
			define('EXTENSIONS_MENUITEMS', EXTENSIONS . '/Menuitems');
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

		if (defined('EXTENSIONS_URL')) {
		} else {
			define('EXTENSIONS_URL', BASE_URL . 'Molajo/Extension');
		}
		if (defined('EXTENSIONS_COMPONENTS_URL')) {
		} else {
			define('EXTENSIONS_COMPONENTS_URL', BASE_URL . 'Molajo/Extension/Component');
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
			if (defined((string)$item['name'])) {
			} else {
				$value = (string)$item['value'];
				define((string)$item['name'], $value);
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
		Services::Registry()->get('ApplicationsParameters');

		if ((int)Services::Registry()->get('Configuration', 'url_force_ssl', 0) > 0) {

			if ((Services::Request()->get('connection')->isSecure() === true)) {

			} else {

				$redirectTo = (string)'https' .
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
	protected function verifySiteApplication()
	{
		$authorise = Services::Authorisation()->verifySiteApplication();
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
