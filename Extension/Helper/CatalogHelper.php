<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

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
	 * @return  bool|object
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
	 * @return    boolean
	 * @since    1.0
	 */
	public function getRoute()
	{
		/** Retrieve the query results */
		$row = $this->get(
			Services::Registry()->get('Parameters', 'request_catalog_id'),
			Services::Registry()->get('Parameters', 'request_url_query')
		);

		/** Store Route Data */
		Services::Registry()->set('Routedata', 'Catalog', $row);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0 || (int)$row['id'] == 0) {
			Services::Registry()->set('Parameters', 'status_found', false);
			return false;
		}

		/** 404: item not routable, redirecting to error page */
		if ((int)$row['routable'] == 0) {
			Services::Registry()->set('Parameters', 'status_found', false);
			return false;
		}

		/** Redirect: routeRequest handles rerouting the request */
		if ((int)$row['redirect_to_id'] == 0) {
		} else {
			Services::Registry()->set('Parameters', 'redirect_to_id', (int)$row['redirect_to_id']);
			return false;
		}

		/** Route Registry */
		Services::Registry()->set('Parameters', 'catalog_id', (int)$row['id']);
		Services::Registry()->set('Parameters', 'catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Parameters', 'catalog_type', $row['b_title']);
		Services::Registry()->set('Parameters', 'catalog_url_sef_request', $row['sef_request']);
		Services::Registry()->set('Parameters', 'catalog_url_request', $row['catalog_url_request']);
		Services::Registry()->set('Parameters', 'catalog_view_group_id', (int)$row['view_group_id']);
		Services::Registry()->set('Parameters', 'catalog_category_id', (int)$row['primary_category_id']);
		Services::Registry()->set('Parameters', 'catalog_source_table', $row['b_source_table']);
		Services::Registry()->set('Parameters', 'catalog_source_id', (int)$row['source_id']);

		/** home */
		if ((int)Services::Registry()->get('Parameters', 'request_catalog_id')
			== Services::Registry()->get('Configuration', 'application_home_catalog_id')
		) {
			Services::Registry()->set('Parameters', 'catalog_home', true);
		} else {
			Services::Registry()->set('Parameters', 'catalog_home', false);
		}

		return true;
	}

	/**
	 * Retrieve Catalog and Catalog Type for specific id or query request
	 *
	 * View Access is verified in Application::Request to identify 403 errors
	 *
	 * @param    int  $catalog_id
	 * @param    null $sef_requst
	 *
	 * @results  object
	 * @since    1.0
	 */
	public function get($catalog_id = 0, $url_sef_request = '', $source_id = 0, $catalog_type_id = 0)
	{
		if ((int)$catalog_id > 0) {

		} else if ((int)$source_id > 0 && (int)$catalog_type_id > 0) {

			$catalog_id = $this->getID((int)$catalog_type_id, (int)$source_id);
			if ($catalog_id == false) {
				return array();
			}

		} else {

			$catalog_id = $this->getIDUsingSEFURL($url_sef_request);
			if ($catalog_id == false) {
				return array();
			}
		}

		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('Catalog', 'Table');

		$m->model->set('id', (int)$catalog_id);
		$m->model->set('process_triggers', 0);
		$row = $m->getData('item');

		if (count($row) == 0) {
			return array();
		}

		$row['catalog_url_request'] = 'index.php?id=' . (int)$row['id'];

		if ($catalog_id == Services::Registry()->get('Configuration', 'application_home_catalog_id', 0)) {
			$row['sef_request'] = '';
		}

		return $row;
	}

	/**
	 * Retrieves Catalog ID for the Request SEF URL
	 *
	 * @param  string $url_sef_request
	 *
	 * @return bool|mixed
	 * @since  1.0
	 */
	public function getIDUsingSEFURL($url_sef_request)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('Catalog', 'Table');
		$m->set('use_special_joins', 0);

		$m->model->query->select($m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn('sef_request') . ' = ' . $m->model->db->q($url_sef_request));

		return $m->getData('item');
	}

	/**
	 * Retrieves Catalog ID for the specified Catalog Type ID and Source ID (From content)
	 *
	 * @param  null $catalog_type_id
	 * @param  null $source_id
	 *
	 * @return bool|mixed
	 * @since  1.0
	 */
	public function getID($catalog_type_id, $source_id)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('Catalog', 'Table');

		$m->model->query->select($m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn('catalog_type_id') . ' = ' . (int)$catalog_type_id);
		$m->model->query->where($m->model->db->qn('source_id') . ' = ' . (int)$source_id);

		return $m->getData('loadResult');
	}

	/**
	 * Retrieves Redirect URL for Catalog id
	 *
	 * @param  integer $catalog_id
	 *
	 * @param  string URL
	 * @since  1.0
	 */
	public function getRedirectURL($catalog_id)
	{
		$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
		$m = new $controllerClass();
		$m->connect('Catalog', 'Table');

		$m->model->query->select($m->model->db->qn('redirect_to_id'));
		$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$catalog_id);

		$result = $m->getData('loadResult');

		if ((int)$result == 0) {
			return false;
		}

		return $this->getURL($result);
	}

	/**
	 * getURL Retrieves URL based on Catalog ID
	 *
	 * @param  integer $catalog_id
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

			$controllerClass = 'Molajo\\MVC\\Controller\\ModelController';
			$m = new $controllerClass();
			$m->connect('Catalog', 'Table');

			$m->model->query->select($m->model->db->qn('sef_request'));
			$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$catalog_id);
			$url = $m->getData('loadResult');

		} else {
			$url = 'index.php?id=' . (int)$catalog_id;
		}

		return $url;
	}
}
