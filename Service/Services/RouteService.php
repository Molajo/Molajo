<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

use Molajo\Application;

use Molajo\Service\Services;

use Molajo\Extension\Helper;

defined('MOLAJO') or die;

/**
 * Route
 *
 * @package    Molajo
 * @subpackage Route
 * @since      1.0
 */
Class RouteService
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
			self::$instance = new RouteService();
		}
		return self::$instance;
	}

	/**
	 * Using the PAGE_REQUEST constant:
	 *
	 *  - retrieve the catalog record
	 *  - set registry values needed to render output
	 *
	 * @return mixed
	 *
	 * @since 1.0
	 */
	public function process()
	{
		/** Dependency Injection */
		if ((int)Services::Registry()->get('Override', 'catalog_id', 0) == 0) {
			Services::Registry()->set('Request', 'catalog_id', 0);
		} else {
			Services::Registry()->set('Request', 'catalog_id',
				(int)Services::Registry()->get('Override', 'catalog_id', 0));
		}
		if (Services::Registry()->get('Override', 'request_url', '') == '') {
			$path = PAGE_REQUEST;
		} else {
			$path = Services::Registry()->get('Override', 'request_url', '');
		}

		/** @var $continue Check for duplicate content URL for Home (and redirect, if found) */
		$continue = $this->checkHome($path);

		if ($continue == false) {
			Services::Debug()->set('Application::Route()->checkHome() Redirect to Real Home');
			return false;
		} else {
			Services::Debug()->set('Application::Route()->checkHome() No Redirect needed');
		}

		/** See if Application is in Offline Mode */
		if (Services::Registry()->get('Configuration', 'offline', 0) == 1) {
			Services::Error()->set(503);
			Services::Debug()->set('Application::Route() Direct to Offline Mode');
			return true;
		} else {
			Services::Debug()->set('Application::Route() Not in Offline Mode');
		}

		/** Remove Nonroutable Parameters from path and save for later use */
		$continue = $this->getNonRoutableParameters();

		if ($continue == false) {
			Services::Debug()->set('Application::Route()->getNonRoutableParameters() Failed');
			return false;
		} else {
			Services::Debug()->set('Application::Route()->getNonRoutableParameters() Successful');
		}

		/**  Get Data: Catalog and Menu Item (Content) */
		$continue = $this->getCatalog();

		if ($continue == false) {
			Services::Registry()->set('Request', 'status_found', false) ;
			Services::Debug()->set('Application::Route()->getCatalog() Failed');
			return false;
		} else {
			Services::Registry()->set('Request', 'status_found', true) ;
			Services::Debug()->set('Application::Route()->getCatalog() Successful');

		}

		if (Services::Registry()->get('Catalog', 'catalog_type_id') == CATALOG_TYPE_MENU_ITEM_COMPONENT) {

			$continue = $this->getMenuitem();

			if ($continue == false) {
				Services::Registry()->set('Request', 'status_found', false) ;
				Services::Debug()->set('Application::Route()->getMenuitem() Failed');
				return false;
			} else {
				Services::Registry()->set('Request', 'status_found', true) ;
				Services::Debug()->set('Application::Route()->getMenuitem() Successful');
			}
		}

		/** 404	 */
		if (Services::Registry()->get('Request', 'status_found') === false) {
			Services::Error()->set(404);
			Services::Debug()->set('Application::Route() 404');
			return false;
		}

		/** URL Change Redirect from Catalog */
		if ((int) Services::Registry()->get('Catalog', 'redirect_to_id', 0) == 0) {
		} else {
			Services::Response()->redirect(
				Application::Helper()->getURL('Catalog',
					Services::Registry()->get('Catalog', 'redirect_to_id', 0)), 301
			);
			Services::Debug()->set('Application::Route() Redirect');
			return false;
		}

		/** Redirect to Logon */
		if (Services::Registry()->get('Configuration', 'logon_requirement', 0) > 0
			&& Services::Registry()->get('User', 'guest', true) === true
			&& Services::Registry()->get('Request', 'catalog_id')
				<> Services::Registry()->get('Configuration', 'logon_requirement', 0)
		) {
			Services::Response()->redirect(
				Services::Registry()->get('Configuration', 'logon_requirement', 0), 303
			);
			Services::Debug()->set('Application::Route() Redirect to Logon');
			return false;
		}

		/**   Return to Application Object */
		return $this;
	}

	/**
	 * Determine if URL is duplicate content for home (and issue redirect, if necessary)
	 *
	 * @param string $path Stripped of Host, Folder, and Application
	 *                         ex. index.php?option=login or access/groups
	 *
	 * @return boolean
	 * @since  1.0
	 */
	protected function checkHome($path = '')
	{

		if (strlen($path) == 0) {
			return true;

		} else {

			/** duplicate content: URLs without the .html */
			if ((int)Services::Registry()->get('Configuration', 'sef_suffix', 1) == 1
				&& substr($path, -11) == '/index.html'
			) {
				$path = substr($path, 0, (strlen($path) - 11));
			}

			if ((int)Services::Registry()->get('Configuration', 'sef_suffix', 1) == 1
				&& substr($path, -5) == '.html'
			) {
				$path = substr($path, 0, (strlen($path) - 5));
			}
		}

		/** populate value used in query  */
		Services::Registry()->set('Request', 'request_url_query', $path);

		/** home: duplicate content - redirect */
		if (Services::Registry()->get('Request', 'request_url_query', '') == 'index.php'
			|| Services::Registry()->get('Request', 'request_url_query', '') == 'index.php/'
			|| Services::Registry()->get('Request', 'request_url_query', '') == 'index.php?'
			|| Services::Registry()->get('Request', 'request_url_query', '') == '/index.php/'
		) {
			Services::Redirect()->set('', 301);
			return false;
		}

		/** Home */
		if (Services::Registry()->get('Request', 'request_url_query', '') == ''
			&& (int)Services::Registry()->get('Request', 'catalog_id', 0) == 0
		) {
			Services::Registry()->set('Request', 'catalog_id',
				Services::Registry()->get('Configuration', 'home_catalog_id', 0));
			Services::Registry()->set('Request', 'request_url_home', true);
		}

		return true;
	}

	/**
	 * Retrieve non routable parameter values and remove from path
	 *
	 * Note: $path has already been stripped of Host, Folder, and Application
	 *
	 *   ex. index.php?option=article&tag=XYZ&prev=6
	 *      ex. access/groups/tag/XYZ/prev/6
	 *
	 * todo: remove tag/value if SEF URL
	 *
	 * @since 1.0
	 */
	protected function getNonRoutableParameters()
	{
		$path = Services::Registry()->get('Request', 'request_url_query');
		if ($path == '') {
			Services::Registry()->get('Request', 'non_routable_parameters', array());
			return true;
		}

		/** save non-routable parameter pairs in array */
		$use = array();

		/** XML with system defined nonroutable pairs */
		$list = Services::Registry()->loadFile('nonroutable');

		foreach ($list->parameter as $item) {

			$key = (string)$item['name'];

			$filter = (string)$item['filter'];
			if ($filter === null) {
				$filter = 'char';
			}

			$value = Services::Request()->get('request')->get($key);

			if ($value === null) {
			} else {

				/** remove non-routable parameter - as it is - from the routeable path */
				$remove = $key . '=' . $value;

				$path = substr($path, 0, strpos($path, $remove))
					. substr($path, strpos($path, $remove) + 1 + strlen($remove), 999);

				/** filter input */
				$value = $this->filterInput($key, $value, $filter, 1, null);

				if ($value === false) {
				} else {
					$use[$key] = $value;
				}
			}
		}

		/** Remove trailing ? or & */
		if (trim($path) == '') {
		} else {
			if (strrpos($path, '&') == (strlen($path) - 1)
				|| strrpos($path, '?') == (strlen($path) - 1)
			) {
				$path = substr($path, 0, strlen($path) - 1);
			}
		}

		/** Update Path and store Non-routable parameters for Extension Use */
		Services::Registry()->set('Request', 'request_url_query', $path);
		Services::Registry()->set('Request', 'non_routable_parameters', $use);

		/** add Edit and Add later

		2. add /add and /edit
		3. deal with nonroutable sef
		 *
		if (strripos($pageRequest, '/edit') == (strlen($pageRequest) - 5)) {
		} else if (strripos($pageRequest, '/add') == (strlen($pageRequest) - 4)) {
		Services::Registry()->set('Request', 'mvc_task', 'add');
		 */

		/**
		look up the URL in the catalog first to determine if it's internal
		if (trim($return) == '') {
		Services::Registry()->set('Request', 'redirect_on_success', '');

		} else if (JUri::isInternal(base64_decode($return))) {
		Services::Registry()->set('Request', 'redirect_on_success', base64_decode($return));

		} else {
		Services::Registry()->set('Request', 'redirect_on_success', '');
		}
		 */
		return true;
	}

	/**
	 * filterInput
	 *
	 * @param   string  $name         Name of input field
	 * @param   string  $field_value  Value of input field
	 * @param   string  $dataType     Datatype of input field
	 * @param   int     $null         0 or 1 - is null allowed
	 * @param   string  $default      Default value, optional
	 *
	 * @return  mixed
	 * @since   1.0
	 *
	 * @throws  /Exception
	 */
	protected function filterInput($name, $value, $dataType, $null, $default)
	{
		try {
			$value = Services::Filter()->filter($value, $dataType, $null, $default);

		} catch (\Exception $e) {
			//echo $e->getMessage() . ' ' . $name;
			return false;
		}

		return $value;
	}

	/**
	 * Retrieve Catalog and Catalog Type data for a specific catalog id
	 * or query request
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	protected function getCatalog()
	{
		/** Retrieve the query results */
		$row = Application::Helper()
			->get('Catalog',
				(int)Services::Registry()->get('Request', 'catalog_id'),
				Services::Registry()->get('Request', 'request_url_query'),
				Services::Registry()->get('Request', 'mvc_option'),
				Services::Registry()->get('Request', 'mvc_id')
			);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0 || (int)$row->routable == 0) {
			return Services::Registry()->set('Request', 'status_found', false);
		}

		/** Redirect: routeRequest handles rerouting the request */
		if ((int)$row->redirect_to_id == 0) {
		} else {
			$this->redirect_to_id = (int)$row->redirect_to_id;
			return Services::Registry()->set('Request', 'status_found', false);
		}

		/** Catalog Registry */
		Services::Registry()->set('Catalog', 'id', (int)$row->id);
		Services::Registry()->set('Catalog', 'redirect_to_id', (int)$row->redirect_to_id);
		Services::Registry()->set('Catalog', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Catalog', 'source_id', (int)$row->source_id);
		Services::Registry()->set('Catalog', 'routable', (int)$row->routable);
		Services::Registry()->set('Catalog', 'view_group_id', (int)$row->view_group_id);
		Services::Registry()->set('Catalog', 'primary_category_id', (int)$row->primary_category_id);
		Services::Registry()->set('Catalog', 'sef_request', $row->sef_request);
		Services::Registry()->set('Catalog', 'request', $row->request);

//todo: remove from table and application
//		Services::Registry()->set('Catalog', 'request_option', $row->request_option);
//		Services::Registry()->set('Catalog', 'request_model', $row->request_model);
		Services::Registry()->set('Catalog', 'source_table', $row->source_table);

		/** home */
		if ((int)Services::Registry()->get('Catalog', 'id', 0)
			== Services::Registry()->get('Configuration', 'home_catalog_id', null)
		) {
			Services::Registry()->set('Request', 'request_url_home', true);
		} else {
			Services::Registry()->set('Request', 'request_url_home', false);
		}

		return true;
	}

	/**
	 * Retrieve Catalog and Catalog Type data for a specific catalog id
	 * or query request
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	protected function getMenuitem()
	{
		/** Retrieve the query results */
		$row = Application::Helper()
			->get('Menuitem',
				(int)Services::Registry()->get('Catalog', 'source_id')
			);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			return Services::Registry()->set('Request', 'status_found', false);
		}

		Services::Registry()->set('Menuitem', 'id', (int)$row->id);
		Services::Registry()->set('Menuitem', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Menuitem', 'title', $row->title);
		Services::Registry()->set('Menuitem', 'alias', $row->alias);
		Services::Registry()->set('Menuitem', 'path', $row->path);
		Services::Registry()->set('Menuitem', 'custom_fields', $row->custom_fields);
		Services::Registry()->set('Menuitem', 'parameters', $row->parameters);
		Services::Registry()->set('Menuitem', 'metadata', $row->metadata);
		Services::Registry()->set('Menuitem', 'source_table', '#__content');
		Services::Registry()->set('Menuitem', 'view_group_id', (int)$row->view_group_id);
		Services::Registry()->set('Menuitem', 'translation_of_id', (int)$row->translation_of_id);
		Services::Registry()->set('Menuitem', 'language', (string)$row->language);
		Services::Registry()->set('Menuitem', 'catalog_id', (int)$row->catalog_id);
		Services::Registry()->set('Menuitem', 'view_group_id', (int)$row->view_group_id);
		Services::Registry()->set('Menuitem', 'menu_id', (int)$row->menu_id);
		Services::Registry()->set('Menuitem', 'menu_catalog_type_id', (int)$row->menu_catalog_type_id);
		Services::Registry()->set('Menuitem', 'menu_title', (string) $row->menu_title);
		Services::Registry()->set('Menuitem', 'menu_parameters', $row->menu_parameters);
		Services::Registry()->set('Menuitem', 'menu_metadata', (string) $row->menu_metadata);
		Services::Registry()->set('Menuitem', 'menu_catalog_id', (int)$row->menu_catalog_id);
		Services::Registry()->set('Menuitem', 'menu_view_group_id', (int)$row->menu_view_group_id);

		$xml = Services::Registry()->loadFile('Content', 'Table');

		Services::Registry()->loadField(
			'MenuitemCustomfields',
			'custom_field',
			$row->custom_fields,
			$xml->custom_fields
		);
		Services::Registry()->loadField(
			'MenuitemMetadata',
			'meta',
			$row->metadata,
			$xml->metadata
		);
		Services::Registry()->loadField(
			'MenuitemParameters',
			'parameter',
			$row->parameters,
			$xml->parameters
		);

		return true;
	}
}
