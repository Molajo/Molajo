<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Helper;

use Molajo\Application;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Catalog Helper
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class CatalogHelper
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * getInstance
	 *
	 * @static
	 * @return bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new CatalogHelper();
		}

		return self::$instance;
	}

	/**
	 * Retrieve Catalog and Catalog Type data for a specific catalog id or query request
	 *
	 * @return boolean
	 * @since    1.0
	 */
	public function getRouteCatalog()
	{
		/** Retrieve the query results */
		Services::Registry()->set('Query', 'Current', 'Catalog getRouteCatalog');

		$item = $this->get(
			Services::Registry()->get('Parameters', 'request_catalog_id'),
			Services::Registry()->get('Parameters', 'request_url_query')
		);

		/** 404: routeRequest handles redirecting to error page */
		if (count($item) == 0 || (int)$item->id == 0 || (int)$item->routable == 0) {
			Services::Registry()->set('Parameters', 'status_found', false);
			Services::Profiler()->set('CatalogHelper->getRouteCatalog 404 - Not Found '
					. ' Requested Catalog ID: ' . Services::Registry()->get('Parameters', 'request_catalog_id')
					. ' Requested URL Query: ' . Services::Registry()->get('Parameters', 'request_url_query'),
				LOG_OUTPUT_ROUTING, 0);

			return false;
		}

		/** Redirect: routeRequest handles rerouting the request */
		if ((int)$item->redirect_to_id == 0) {
		} else {
			Services::Profiler()->set('CatalogHelper->getRouteCatalog Redirect to ID '
				. (int)$item->redirect_to_id, LOG_OUTPUT_ROUTING, 0);

			Services::Registry()->set('Parameters', 'redirect_to_id', (int)$item->redirect_to_id);

			return false;
		}

		/** Route Registry */
		Services::Registry()->set('Parameters', 'catalog_id', (int)$item->id);
		Services::Registry()->set('Parameters', 'catalog_type_id', (int)$item->catalog_type_id);
		Services::Registry()->set('Parameters', 'catalog_type', $item->b_title);
		Services::Registry()->set('Parameters', 'catalog_url_sef_request', $item->sef_request);
		Services::Registry()->set('Parameters', 'catalog_url_request', $item->catalog_url_request);
		Services::Registry()->set('Parameters', 'catalog_menuitem_type', $item->menuitem_type);
		Services::Registry()->set('Parameters', 'catalog_view_group_id', (int)$item->view_group_id);
		Services::Registry()->set('Parameters', 'catalog_category_id', (int)$item->primary_category_id);
		Services::Registry()->set('Parameters', 'catalog_extension_instance_id', $item->b_extension_instance_id);
		Services::Registry()->set('Parameters', 'catalog_model_type', $item->b_model_type);
		Services::Registry()->set('Parameters', 'catalog_model_name', $item->b_model_name);
		Services::Registry()->set('Parameters', 'catalog_slug', $item->b_slug);
		Services::Registry()->set('Parameters', 'catalog_source_table', $item->b_source_table);
		Services::Registry()->set('Parameters', 'catalog_source_id', (int)$item->source_id);
		Services::Registry()->set('Parameters', 'catalog_tinyurl', (int)$item->tinyurl);

		/** home */
		if ((int)Services::Registry()->get('Parameters', 'catalog_id')
			== Services::Registry()->get('Configuration', 'application_home_catalog_id')
		) {
			Services::Registry()->set('Parameters', 'catalog_home', 1);
		} else {
			Services::Registry()->set('Parameters', 'catalog_home', 0);
		}

		return true;
	}

	/**
	 * Retrieve Catalog and Catalog Type for specific id or query request
	 *
	 * View Access is verified in Application::Request to identify 403 errors
	 *
	 * @param int    $catalog_id
	 * @param string $url_sef_request
	 * @param int    $source_id
	 * @param int    $catalog_type_id
	 *
	 * @return array
	 * @since   1.0
	 */
	public function get($catalog_id = 0, $url_sef_request = '', $source_id = 0, $catalog_type_id = 0)
	{
		if ((int)$catalog_id > 0) {

		} elseif ((int)$source_id > 0 && (int)$catalog_type_id > 0) {
			$catalog_id = $this->getID((int)$catalog_type_id, (int)$source_id);
			if ($catalog_id == false) {
				return array();
			}

		} else {
			$catalog_id = $this->getIDUsingSEFURL($url_sef_request);
			if ((int)$catalog_id == 0) {
				return array();
			}
		}

		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();

		$results = $m->connect('Table', 'Catalog');
		if ($results == false) {
			return false;
		}

		$m->set('id', (int)$catalog_id);
		$m->set('use_special_joins', 1);
		$m->set('process_plugins', 0);

		$item = $m->getData('item');

		if (count($item) == 0) {
			return array();
		}

		$item->catalog_url_request = 'index.php?id=' . (int)$item->id;

		if ($catalog_id == Services::Registry()->get('Configuration', 'application_home_catalog_id', 0)) {
			$item->sef_request = '';
		}

		return $item;
	}

	/**
	 * Retrieves Catalog ID for the Request SEF URL
	 *
	 * @param string $url_sef_request
	 *
	 * @return bool|mixed
	 * @since  1.0
	 */
	public function getIDUsingSEFURL($url_sef_request)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();
		$results = $m->connect('Table', 'Catalog');
		if ($results == false) {
			return false;
		}

		$m->model->query->select($m->model->db->qn('a') . '.' . $m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn('sef_request') . ' = ' . $m->model->db->q($url_sef_request));

		return $m->getData('result');
	}

	/**
	 * Retrieves Catalog ID for the specified Catalog Type ID and Source ID (From content)
	 *
	 * @param null $catalog_type_id
	 * @param null $source_id
	 *
	 * @return bool|mixed
	 * @since  1.0
	 */
	public function getID($catalog_type_id, $source_id = null)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();
		$results = $m->connect('Table', 'Catalog');
		if ($results == false) {
			return false;
		}

		$m->model->query->select($m->model->db->qn('a') . '.' . $m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn('a') . '.' . $m->model->db->qn('catalog_type_id')
			. ' = ' . (int)$catalog_type_id);
		$m->model->query->where($m->model->db->qn('a') . '.' . $m->model->db->qn('source_id')
			. ' = ' . (int)$source_id);

		return $m->getData('result');
	}

	/**
	 * Retrieves Redirect URL for Catalog id
	 *
	 * @param integer $catalog_id
	 *
	 * @return string URL
	 * @since  1.0
	 */
	public function getRedirectURL($catalog_id)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
		$m = new $controllerClass();
		$results = $m->connect('Table', 'Catalog');
		if ($results == false) {
			return false;
		}

		$m->model->query->select($m->model->db->qn('a') . '.' . $m->model->db->qn('redirect_to_id'));
		$m->model->query->where($m->model->db->qn('a') . '.' . $m->model->db->qn('id') . ' = ' . (int)$catalog_id);

		$result = $m->getData('result');

		if ((int)$result == 0) {
			return false;
		}

		return $this->getURL($result);
	}

	/**
	 * getURL Retrieves URL based on Catalog ID
	 *
	 * @param integer $catalog_id
	 *
	 * @return string
	 * @since  1.0
	 */
	public function getURL($catalog_id)
	{
		if ($catalog_id == Services::Registry()->get('Configuration', 'application_home_catalog_id', 0)) {
			return '';
		}

		if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {

			$controllerClass = 'Molajo\\MVC\\Controller\\Controller';
			$m = new $controllerClass();

			$results = $m->connect('Table', 'Catalog');
			if ($results == false) {
				return false;
			}

			$m->model->query->select($m->model->db->qn('a') . '.' . $m->model->db->qn('sef_request'));
			$m->model->query->where($m->model->db->qn('a') . '.' . $m->model->db->qn('id') . ' = ' . (int)$catalog_id);
			$m->model->query->where($m->model->db->qn('a') . '.' . $m->model->db->qn('redirect_to_id') . ' = ' . (int)0);
			$m->model->query->where($m->model->db->qn('a') . '.' . $m->model->db->qn('routable') . ' = ' . (int)1);

			$url = $m->getData('result');

		} else {
			$url = 'index.php?id=' . (int)$catalog_id;
		}

		return $url;
	}
}
