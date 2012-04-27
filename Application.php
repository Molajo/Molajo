<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo;

use Molajo\MVC\Model\TableModel;

use Molajo\Extension\Helper;

use Molajo\Service\Services;

use Molajo\Service\Services\RequestService;

use Molajo\Service\Services\RegistryService;

use Joomla\JFactory;

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
	 * Application::Helper
	 *
	 * @var    object Helper
	 * @since  1.0
	 */
	protected static $helper = null;

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
	 * @param string $override_request_url
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
	 * todo: Add events
	 *
	 * @return  mixed
	 * @since   1.0
	 */
	public function process($override_request_url = null, $override_catalog_id = null,
							$override_sequence_xml = null, $override_final_xml = null)
	{
		/** Initialise */
		$continue = $this->initialise();

		Services::Registry()->set('Override', 'request_url', $override_request_url);
		Services::Registry()->set('Override', 'catalog_id', $override_catalog_id);
		Services::Registry()->set('Override', 'sequence_xml', $override_sequence_xml);
		Services::Registry()->set('Override', 'final_xml', $override_final_xml);

		if ($continue == false) {
			Services::Debug()->set('Application Initialise failed');
			return;
		} else {
			Services::Debug()->set('Application Initialise succeeded');
		}

		/** Route */
		$continue = $this->route();

		if ($continue == false) {
			Services::Debug()->set('Application Route failed');
			return;
		} else {
			Services::Debug()->set('Application Route succeeded');
		}

		/** Authorise */
		if (Services::Registry()->get('Request', 'status_found') === true) {
			$continue = $this->authorise();
		}

		if ($continue == false) {
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
	 * @return  boolean
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

		/** Retrieve Site data and save in registry */
		$continue = $this->setSiteData();
		if ($continue == false) {
			return false;
		}

		/** Verify that this site is authorised to access this application */
		$continue = $this->getSiteApplicationAuthorisation();
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
	 * route application
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function route()
	{
		Services::Route()->process();

		if (Services::Redirect()->url === null
			&& (int)Services::Redirect()->code == 0
		) {
		} else {
			return false;
		}

		return true;
	}

	/**
	 * Verify user authorization
	 *
	 * @return   boolean
	 * @since    1.0
	 */
	protected function authorise()
	{
		/** 403: authoriseTask handles redirecting to error page */
		if (in_array(Services::Registry()->get('Catalog', 'view_group_id'),
			Services::Registry()->get('User', 'ViewGroups'))
		) {
			Services::Registry()->set('Request', 'status_authorised', true);
		} else {
			return Services::Registry()->set('Request', 'status_authorised', false);
		}

		/** display view verified in getCatalog */
		if (Services::Registry()->get('Request', 'mvc_task') == 'display'
			&& Services::Registry()->get('Request', 'status_authorised') === true
		) {
			return true;
		}
		if (Services::Registry()->get('Request', 'mvc_task') == 'display'
			&& Services::Registry()->get('Request', 'status_authorised') === false
		) {
			Services::Error()->set(403);
			return false;
		}

		/** verify other tasks */
		Services::Registry()->set('Request', 'status_authorised',
			Services::Authorisation()->authoriseTask(
				Services::Registry()->get('Request', 'mvc_task'),
				Services::Registry()->get('Request', 'catalog_id')
			)
		);

		if (Services::Registry()->get('Request', 'status_authorised') === true) {
		} else {
			Services::Error()->set(403);
			return false;
		}

		return true;
	}

	/**
	 * execute the action requested
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function execute()
	{
		$action = Services::Registry()->get('Request', 'mvc_controller', 'display');

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
	 * Executes a display task
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
	 * @param  string $override_sequenceXML
	 * @param  string $override_finalXML
	 *
	 * @return  Application
	 */
	protected function display($override_sequenceXML = null, $override_finalXML = null)
	{
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
		/** Action: Database action */
		$temp = Services::Registry()->initialise();
		$temp->loadArray($this->parameters);
		$this->parameters = $temp;

		if (Services::Registry()->get('Configuration', 'sef', 1) == 0) {
			$link = $this->page_request->get('request_url_sef');
		} else {
			$link = $this->page_request->get('request_url');
		}
		Services::Registry()->set('Request', 'redirect_on_failure', $link);

		Services::Registry()->set('Request', 'model',
			ucfirst(trim(Services::Registry()->get('Request', 'mvc_model'))) . 'Model');
		$cc = 'Molajo' . ucfirst(Services::Registry()->get('Request', 'mvc_controller')) . 'Controller';
		Services::Registry()->set('Request', 'controller', $cc);
		$task = Services::Registry()->get('Request', 'mvc_task');
		Services::Registry()->set('Request', 'task', $task);
		Services::Registry()->set('Request', 'id', Services::Registry()->get('Request', 'mvc_id'));
		$controller = new $cc($this->page_request, $this->parameters);

		/** execute task: non-display, edit, or add tasks */
		$continue = $controller->$task();

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
			&& (int)Services::Redirect()->code == 0
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
	 * @return  boolean
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
	 * @return  boolean
	 * @since   1.0
	 */
	protected function setDefines()
	{

//todo: now that namespaces are used, how much of this is really needed?

		/** Override Hint */
		if (file_exists(BASE_FOLDER . '/defines.php')) {
			include_once BASE_FOLDER . '/defines.php';
		}

		if (defined('EXTENSIONS')) {
		} else {
			define('EXTENSIONS', BASE_FOLDER . '/Molajo/Extension');
		}
		if (defined('SITES')) {
		} else {
			define('SITES', BASE_FOLDER . '/site');
		}
		if (defined('CONFIGURATION_FOLDER')) {
		} else {
			define('CONFIGURATION_FOLDER', BASE_FOLDER . '/Molajo/Configuration');
		}

		if (defined('MVC')) {
		} else {
			define('MVC', APPLICATIONS . '/MVC');
		}
		if (defined('MVC_URL')) {
		} else {
			define('MVC_URL', BASE_URL . 'Molajo/MVC');
		}

		if (defined('EXTENSIONS_HELPER')) {
		} else {
			define('EXTENSIONS_HELPER', EXTENSIONS . '/Helper');
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
		if (defined('EXTENSIONS_VIEWS')) {
		} else {
			define('EXTENSIONS_VIEWS', EXTENSIONS . '/View');
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
		if (defined('EXTENSIONS_VIEWS_URL')) {
		} else {
			define('EXTENSIONS_VIEWS_URL', BASE_URL . 'Molajo/Extension/View');
		}


		if (defined('SERVICES')) {
		} else {
			define('SERVICES', APPLICATIONS . '/Service');
		}
		if (defined('TRIGGERS')) {
		} else {
			define('TRIGGERS', BASE_URL . 'Service/Trigger');
		}

		/**
		 *  Allows for quoting in language .ini files.
		 */
		if (defined('LANGUAGE_QUOTE_REPLACEMENT')) {
		} else {
			define('LANGUAGE_QUOTE_REPLACEMENT', '"');
		}

		/** Define PHP constants for application variables */
		//$defines = Services::Registry()->loadFile('defines');
		$defines = RegistryService::loadFile('defines');
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
	 * @return  boolean
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

		if (defined('SITE_LANGUAGES')) {
		} else {
			define('SITE_LANGUAGES', SITES_MEDIA_FOLDER . '/Language');
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

		$scheme = Application::Request()->get('request')->getScheme() . '://';
		$siteBase = substr(BASE_URL, strlen($scheme), strlen(BASE_URL) - strlen($scheme));

		if (defined('SITE_BASE_URL')) {
		} else {

			$sites = RegistryService::loadFile('sites');

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

		return true;
	}

	/**
	 * Identify current application and page request
	 *
	 * @return  boolean
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
			/* must also define PAGE_REQUEST */
		} else {

			$apps = RegistryService::loadFile('applications');

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
	 * @return  bool
	 * @since   1.0
	 */
	protected function sslCheck()
	{
		if ((int)Services::Registry()->get('Configuration', 'force_ssl', 0) > 0) {

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
	 * Retrieve Site data and save in registry
	 *
	 * @return mixed
	 * @since  1.0
	 * @throws \RuntimeException
	 */
	protected function setSiteData()
	{
		$m = new TableModel ('Sites', SITE_ID);

		$m->query->where($m->db->qn('id') . ' = ' . (int)SITE_ID);

		$results = $m->loadAssoc();

		if ($results === false) {
			throw new \RuntimeException ('Application setSiteData() query problem');
		}

		/** Registry for Custom Fields and Metadata */
		$xml = Services::Registry()->loadFile('Sites');

		Services::Registry()->loadField('SiteCustomfields', 'custom_fields',
			$results['custom_fields'], $xml->custom_fields);
		Services::Registry()->loadField('SiteMetadata', 'meta',
			$results['metadata'], $xml->metadata);
		Services::Registry()->loadField('SiteParameters', 'parameters',
			$results['parameters'], $xml->parameter);

		$this->base_url = $results['base_url'];

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
	 * @return  Services
	 * @throws  \RuntimeException
	 * @since   1.0
	 */
	public static function Services()
	{
		if (self::$services) {
		} else {
			try {
				self::$services = Services::getInstance();
			}
			catch (\RuntimeException $e) {
				echo 'Instantiate Service Exception : ', $e->getMessage(), "\n";
				die;
			}
		}
		return self::$services;
	}

	/**
	 * Application::Helper
	 *
	 * @static
	 * @return  Helper
	 * @throws  \RuntimeException
	 * @since   1.0
	 */
	public static function Helper()
	{
		if (self::$helper) {
		} else {
			try {
				self::$helper = Helper::getInstance();
			}
			catch (\Exception $e) {
				echo 'Instantiate Helper Exception : ', $e->getMessage(), "\n";
				die;
			}
		}
		return self::$helper;
	}

	/**
	 * Application::Request
	 *
	 * @static
	 * @return  Request
	 * @since   1.0
	 */
	public static function Request()
	{
		if (self::$request) {
		} else {
			try {
				self::$request = RequestService::getInstance();
			}
			catch (\Exception $e) {
				echo 'Instantiate RequestService Exception : ', $e->getMessage(), "\n";
				die;
			}
		}

		return self::$request;
	}
}
