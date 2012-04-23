<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Application;

use Molajo\Application\Services;

use Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Route
 *
 * @package    Molajo
 * @subpackage Route
 * @since      1.0
 */
Class Route
{
	/**
	 * $instance
	 *
	 * @var        object
	 * @since      1.0
	 */
	protected static $instance = null;

	/**
	 * Returns the global site object, creating if not existing
	 *
	 * @return  Object
	 *
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (self::$instance) {
		} else {
			self::$instance = new Route();
		}
		return self::$instance;
	}

	/**
	 * Using the PAGE_REQUEST constant:
	 *
	 *  - retrieve the asset record
	 *  - set registry values needed to render output
	 *
	 * @return mixed
	 *
	 * @since 1.0
	 */
	public function process()
	{
		/**
		 * 	Dependency Injection
		 */
		if ((int)Services::Registry()->get('Override\\asset_id', 0) == 0) {
			Services::Registry()->set('Request\\asset_id', 0);
		} else {
			Services::Registry()->set('Request\\asset_id',
				(int)Services::Registry()->get('Override\\asset_id', 0));
		}
		if (Services::Registry()->get('Override\\request_url', '') == '') {
			$path = PAGE_REQUEST;
		} else {
			$path = Services::Registry()->get('Override\\request_url', '');
		}

		/**
		 * 	Check for duplicate content URL for Home (and redirect, if found)
		 */
		$continue = $this->checkHome($path);

		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->checkHome() Redirect to Real Home');
			return false;
		} else {
			Services::Debug()->set('Molajo::Route()->checkHome() No Redirect needed');
		}

		/**
		 * 	See if Application is in Offline Mode
		 */
		if (Services::Registry()->get('Configuration\\offline', 1) == 0) {
			Services::Error()->set(503);
			Services::Debug()->set('Molajo::Route() Direct to Offline Mode');
			return false;
		} else {
			Services::Debug()->set('Molajo::Route() Not in Offline Mode');
		}

		/**
		 * 	Get Request Object
		 */
		$continue = $this->getRequest();

		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->getRequest() Failed');
			return false;
		} else {
			Services::Debug()->set('Molajo::Route()->getRequest() Successful');
		}

		/**
		 * 	Get Asset Data
		 */
		$continue = $this->getAsset();

		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->getAsset() Failed');
			return false;
		} else {
			Services::Debug()->set('Molajo::Route()->getAsset() Successful');
		}

		/**
		 * 	404
		 */
		if (Services::Registry()->get('Request\\status_found') === false) {
			Services::Error()->set(404);
			Services::Debug()->set('Molajo::Route() 404');
			return false;
		}

		/**
		 * 	Asset Redirect
		 */
		if ($this->redirect_to_id == 0) {
		} else {
			Services::Response()->redirect(
				Molajo::Helper()->getURL('Asset', $this->redirect_to_id), 301
			);
			Services::Debug()->set('Molajo::Route() Redirect');
			return false;
		}

		/**
		 * 	Redirect to Logon
		 */
		if (Services::Registry()->get('Configuration\\logon_requirement', 0) > 0
			&& Services::Registry()->get('User\\guest', true) === true
			&& Services::Registry()->get('Request\\asset_id')
				<> Services::Registry()->get('Configuration\\logon_requirement', 0)
		) {
			Services::Response()->redirect(
				Services::Registry()->get('Configuration\\logon_requirement', 0), 303
			);
			Services::Debug()->set('Molajo::Route() Redirect to Logon');
			return false;
		}

		/**
		 * 	Return to Application Object
		 */
		return $this;
	}

	/**
	 * Determine if URL is duplicate content for home (and issue redirect, if necessary)
	 *
	 * @param string $path Stripped of Host, Folder, and Application
	 * 						ex. index.php?option=login or access/groups
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function checkHome($path)
	{
		/** duplicate content: URLs without the .html */
		if ((int)Services::Registry()->get('Configuration\\sef_suffix', 1) == 1
			&& substr($path, -11) == '/index.html'
		) {
			$path = substr($path, 0, (strlen($path) - 11));
		}
		if ((int)Services::Registry()->get('Configuration\\sef_suffix', 1) == 1
			&& substr($path, -5) == '.html'
		) {
			$path = substr($path, 0, (strlen($path) - 5));
		}

		/** populate value used in query  */
		Services::Registry()->set('Request\\request_url_query', $path);

		/** home: duplicate content - redirect */
		if (Services::Registry()->get('Request\\request_url_query', '') == 'index.php'
			|| Services::Registry()->get('Request\\request_url_query', '') == 'index.php/'
			|| Services::Registry()->get('Request\\request_url_query', '') == 'index.php?'
			|| Services::Registry()->get('Request\\request_url_query', '') == '/index.php/'
		) {
			Services::Redirect()->set('', 301);
			return false;
		}

		/** Home */
		if (Services::Registry()->get('Request\\request_url_query', '') == ''
			&& (int)Services::Registry()->get('Request\\asset_id', 0) == 0
		) {
			Services::Registry()->set('Request\\asset_id',
				Services::Registry()->get('Configuration\\home_asset_id', 0));
			Services::Registry()->set('Request\\request_url_home', true);
		}

		return true;
	}

	/**
	 * Retrieve URL contents
	 *
	 * @return bool
	 *
	 * @since 1.0
	 */
	protected function getRequest()
	{
		/**
		echo 'Ajax ' . Services::Request()->request->isXmlHttpRequest().'<br />';
		$queryString = Services::Request()->get('option');
		 */

		$queryString = Services::Request()->request->getQueryString();
		$pair = explode('&', $queryString);
		$pairs = array();
		$extra = array();

		if (count($pairs) > 0) {
			$xml = CONFIGURATION_FOLDER . '/parameters.xml';
			if (is_file($xml)) {
			} else {
				return false;
			}
			$parameters = simplexml_load_file($xml);
			foreach ($parameters->parameter as $item) {
				$extra[(string)$item] = null;
			}
		}

		foreach ($pair as $item) {
			$kv = explode('=', $item);
			$pairs[$kv[0]] = $kv[1];
		}

		/** todo: input is not filtered yet */

		if (count($pairs) > 0
			&& isset($pairs['task'])
		) {
			Services::Registry()->set('Request\\mvc_task', $pairs['task']);
		} else {
			Services::Registry()->set('Request\\mvc_task', 'display');
		}

		if (Services::Registry()->get('Request\\mvc_task', '') == ''
			|| Services::Registry()->get('Request\\mvc_task', 'display') == 'display'
		) {
			$pageRequest = Services::Registry()->get('Request\\request_url_query');

			if (strripos($pageRequest, '/edit') == (strlen($pageRequest) - 5)) {
				$pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/edit'));
				Services::Registry()->set('Request\\request_url_query', $pageRequest);
				Services::Registry()->set('Request\\mvc_task', 'edit');

			} else if (strripos($pageRequest, '/add') == (strlen($pageRequest) - 4)) {
				$pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/add'));
				Services::Registry()->set('Request\\request_url_query', $pageRequest);
				Services::Registry()->set('Request\\mvc_task', 'add');

			} else {
				Services::Debug()->set('Molajo::Request()->getRequest() complete Display Task');
				Services::Registry()->set('Request\\mvc_task', 'display');
			}

			return true;
		}

		/** return */
		if (isset($pairs['return'])) {
			$return = $pairs['return'];
		} else {
			$return = '';
		}

		if (trim($return) == '') {
			Services::Registry()->set('Request\\redirect_on_success', '');

		} else if (JUri::isInternal(base64_decode($return))) {
			Services::Registry()->set('Request\\redirect_on_success', base64_decode($return));

		} else {
			Services::Registry()->set('Request\\redirect_on_success', '');
		}

		/** option */
		Services::Registry()->set('Request\\mvc_option', (string)$pairs['option']);

		/** asset information */
		Services::Registry()->set('Request\\mvc_id', (int)$pairs['id']);

		Services::Debug()->set('Molajo::Request()->getRequest()');

		return true;
	}

	/**
	 * Retrieve Asset and Asset Type data for a specific asset id
	 * or query request
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	protected function getAsset()
	{
		/** Retrieve the query results */
		$row = Molajo::Helper()
			->get('Asset',
				(int)Services::Registry()->get('Request\\asset_id'),
				Services::Registry()->get('Request\\request_url_query'),
				Services::Registry()->get('Request\\mvc_option'),
				Services::Registry()->get('Request\\mvc_id')
			);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0
			|| (int)$row->routable == 0
		) {
			return Services::Registry()->set($ns . '\\status_found', false);
		}

		/** Redirect: routeRequest handles rerouting the request */
		if ((int)$row->redirect_to_id == 0) {
		} else {
			$this->redirect_to_id = (int)$row->redirect_to_id;
			return Services::Registry()->set($ns . '\\status_found', false);
		}

		/** 403: authoriseTask handles redirecting to error page */
		if (in_array($row->view_group_id, Services::Registry()->get('User\\view_groups'))) {
			Services::Registry()->set($ns . '\\status_authorised', true);
		} else {
			return Services::Registry()->set($ns . '\\status_authorised', false);
		}

		$continue = $this->setRegistryValues('Asset', $row);
		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->getData() for Asset failed');
			return;
		} else {
			Services::Debug()->set('Molajo::Route()->getData() for Asset succeeded');
		}

///////

		if (Services::Registry()->get($ns . '\\request_asset_type_id')
			== ASSET_TYPE_MENU_ITEM_COMPONENT
		) {
			Services::Registry()->set($ns . '\\menu_item_id', $row->source_id);
			$this->setRegistryValues('Menuitem', $row);
			if (Services::Registry()->get($ns . '\\status_found') === false) {
				return Services::Registry()->get($ns . '\\status_found');
			}
		} else {
			Services::Registry()->set($ns . '\\source_id', $row->source_id);

		}

//////


		/** Menu Item */
		$row = Molajo::Helper()
			->get('Menuitem',
				(int)Services::Registry()->get('Request\\asset_id'),
				Services::Registry()->get('Request\\request_url_query'),
				Services::Registry()->get('Request\\mvc_option'),
				Services::Registry()->get('Request\\mvc_id')
			);

		$continue = $this->setRegistryValues('Menuitem', $row);
		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->getData() for Menu Item failed');
			return;
		} else {
			Services::Debug()->set('Molajo::Route()->getData() for Menu Item succeeded');
		}

		/** Source */
		$row = Molajo::Helper()
			->get('Source',
				(int)Services::Registry()->get('Request\\asset_id'),
				Services::Registry()->get('Request\\request_url_query'),
				Services::Registry()->get('Request\\mvc_option'),
				Services::Registry()->get('Request\\mvc_id')
			);

		$continue = $this->setRegistryValues('Menuitem', $row);
		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->getData() for Source failed');
			return;
		} else {
			Services::Debug()->set('Molajo::Route()->getData() for Source succeeded');
		}

		/** Category */
		$row = Molajo::Helper()
			->get('Category',
				(int)Services::Registry()->get('Request\\asset_id'),
				Services::Registry()->get('Request\\request_url_query'),
				Services::Registry()->get('Request\\mvc_option'),
				Services::Registry()->get('Request\\mvc_id')
			);

		$continue = $this->setRegistryValues('Category', $row);

		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->getData() for Category failed');
			return;
		} else {
			Services::Debug()->set('Molajo::Route()->getData() for Category succeeded');
		}

		/** Extension */
		$row = Molajo::Helper()
			->get('Extension',
				(int)Services::Registry()->get('Request\\asset_id'),
				Services::Registry()->get('Request\\request_url_query'),
				Services::Registry()->get('Request\\mvc_option'),
				Services::Registry()->get('Request\\mvc_id')
			);

		$continue = $this->setRegistryValues('Extension', $row);

		if ($continue == false) {
			Services::Debug()->set('Molajo::Route()->getData() for Extension failed');
			return;
		} else {
			Services::Debug()->set('Molajo::Route()->getData() for Extension succeeded');
		}


		return Services::Registry()->get($ns . '\\status_found');
	}

	/**
	 * Process the output from assets, menu items, content, categories, and
	 * extensions to establish registry values to be used in task execution
	 *
	 * @param $ns  - namespace for registry
	 * @param $row
	 *
	 * @return mixed
	 *
	 * @since  1.0
	 */
	protected function setRegistryValues($ns, $row)
	{
		Services::Registry()->set($ns . '\\id', (int)$row->id);
		Services::Registry()->set($ns . '\\title', (string)$row->title);
		Services::Registry()->set($ns . '\\alias', (string)$row->alias);
		Services::Registry()->set($ns . '\\asset_type_id', (int)$row->asset_type_id);
		Services::Registry()->set($ns . '\\asset_id', (int)$row->asset_id);
		Services::Registry()->set($ns . '\\view_group_id', (int)$row->view_group_id);
		Services::Registry()->set($ns . '\\language', (int)$row->language);
		Services::Registry()->set($ns . '\\translation_of_id', (int)$row->translation_of_id);

		$xml = simplexml_load_file(APPLICATIONS_MVC . '/Model/Table/'.strtolower($ns).'xml');

		Services::Registry()->loadField($ns . 'Customfields\\', 'custom_fields', $row->custom_fields, $xml->custom_fields);
		Services::Registry()->loadField($ns . 'Metadata\\', 'meta', $row->metadata, $xml->metadata);
		Services::Registry()->loadField($ns . 'Parameters\\', 'parameters', $row->parameters, $xml->parameter);

		if ($ns == 'Asset') {
			Services::Registry()->set($ns . '\\request_url', $row->request);
			Services::Registry()->set($ns . '\\request_url_sef', $row->sef_request);

			/** home */
			if ((int)Services::Registry()->get($ns . '\\asset_id', 0)
				== Services::Registry()->get('Configuration\\home_asset_id', null)
			) {
				Services::Registry()->set($ns . '\\request_url_home', true);
			} else {
				Services::Registry()->set($ns . '\\request_url_home', false);
			}

			return Services::Registry()->get($ns . '\\status_found');
		}

		if ($ns == 'Menuitem') {
		}

		if ($ns == 'Source') {
			Services::Registry()->set($ns . '\\source_table', $row->view_group_id);
			Services::Registry()->set($ns . '\\source_last_modified', (int)$row->view_group_id);
		}

		if ($ns == 'Category') {
			Services::Registry()->set($ns . '\\category_id', (int)$row->primary_category_id);

			/** primary category */
			if (Services::Registry()->get($ns . '\\category_id', 0) == 0) {
			} else {
				Services::Registry()->set($ns . '\\mvc_category_id',
					Services::Registry()->get($ns . '\\category_id'));

			}
		}

		if ($ns == 'Extension') {
			Services::Registry()->set($ns . '\\extension_path', '');
			Services::Registry()->set($ns . '\\extension_type', '');
			Services::Registry()->set($ns . '\\extension_event_type', '');

			/** mvc options and url parameters */
			Services::Registry()->set($ns . '\\extension_instance_name', $row->request_option);
			Services::Registry()->set($ns . '\\mvc_model', $row->request_model);
			Services::Registry()->set($ns . '\\mvc_id', (int)$row->source_id);

			Services::Registry()->set($ns . '\\mvc_controller',
				Services::Access()
					->getTaskController(Services::Registry()->get($ns . '\\mvc_task'))
			);

			/** Action Tasks need no additional information */
			if (Services::Registry()->get($ns . '\\mvc_controller') == 'display') {
			} else {
				return Services::Registry()->set($ns . '\\status_found', true);
			}
		}

	}

	/**
	 * Create and Initialize the request registries so that the data
	 * for all rendering (or other actions)
	 *
	 * @return   null
	 *
	 * @since    1.0
	 */
	protected function copyRequestRegistry()
	{
		Services::Registry()->copy('Asset', 'RequestAsset');
		Services::Registry()->copy('Menuitem', 'RequestMenuitem');
		Services::Registry()->copy('Source', 'RequestSource');
		Services::Registry()->copy('Category', 'RequestCategory');
		Services::Registry()->copy('Extension', 'RequestExtension');
		Services::Registry()->copy('MVC', 'RequestMVC');
		Services::Registry()->copy('Template', 'RequestTemplate');
		Services::Registry()->copy('Wrap', 'RequestWrap');
		Services::Registry()->copy('Parameters', 'RequestParameters');
		Services::Registry()->copy('Metadata', 'RequestMetadata');
	}

	protected function getValues()
	{

		/** request */
		Services::Registry()->set($ns . '\\request_url_base', BASE_URL);
		Services::Registry()->set($ns . '\\request_asset_id', 0);
		Services::Registry()->set($ns . '\\request_asset_type_id', 0);
		Services::Registry()->set($ns . '\\request_url_query', '');
		Services::Registry()->set($ns . '\\request_url', '');
		Services::Registry()->set($ns . '\\request_url_sef', '');
		Services::Registry()->set($ns . '\\request_url_home', false);


		/** mvc parameters */
		Services::Registry()->set($ns . '\\mvc_controller', '');
		Services::Registry()->set($ns . '\\mvc_option', '');
		Services::Registry()->set($ns . '\\mvc_task', '');
		Services::Registry()->set($ns . '\\mvc_model', '');
		Services::Registry()->set($ns . '\\mvc_id', 0);
		Services::Registry()->set($ns . '\\mvc_category_id', 0);
		Services::Registry()->set($ns . '\\mvc_url_parameters', array());
		Services::Registry()->set($ns . '\\mvc_suppress_no_results', false);

		/** results */
		Services::Registry()->set($ns . '\\error_status', false);
		Services::Registry()->set($ns . '\\status_authorised', false);
		Services::Registry()->set($ns . '\\status_found', false);
	}
}
