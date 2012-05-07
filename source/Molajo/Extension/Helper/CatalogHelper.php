<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * CatalogHelper
 *
 * @package       Molajo
 * @subpackage    Service
 * @since         1.0
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
	 * @since  1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new CatalogHelper();
		}
		return self::$instance;
	}

	/**
	 * Retrieve Catalog and Catalog Type data for a specific catalog id
	 * or query request
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	public function getRoute()
	{

		/** Retrieve the query results */
		$row = $this->get(
			Services::Registry()->get('Route', 'id'),
			Services::Registry()->get('Route', 'request_url_query'),
			Services::Registry()->get('Route', 'source_id')
		);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0 || (int)$row['id'] == 0) {
			Services::Registry()->set('Route', 'status_found', false);
			return false;
		}

		/** Redirect: routeRequest handles rerouting the request */
		if ((int)$row['redirect_to_id'] == 0) {
		} else {
			Services::Registry()->set('Route', 'redirect_to_id', (int)$row['redirect_to_id']);
			return false;
		}

		/** Catalog Registry */
		Services::Registry()->set('Route', 'id', (int)$row['id']);
		Services::Registry()->set('Route', 'catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Route', 'catalog_type', $row['title']);
		Services::Registry()->set('Route', 'source_table', $row['source_table']);
		Services::Registry()->set('Route', 'source_id', (int)$row['source_id']);
		Services::Registry()->set('Route', 'primary_category_id', (int)$row['primary_category_id']);
		Services::Registry()->set('Route', 'sef_request', $row['sef_request']);
		Services::Registry()->set('Route', 'request', $row['request']);
		Services::Registry()->set('Route', 'redirect_to_id', (int)$row['redirect_to_id']);
		Services::Registry()->set('Route', 'routable', (int)$row['routable']);
		Services::Registry()->set('Route', 'view_group_id', (int)$row['view_group_id']);

		/** home */
		if ((int)Services::Registry()->get('Route', 'id')
			== Services::Registry()->get('Configuration', 'home_catalog_id')
		) {
			Services::Registry()->set('Route', 'home', true);
		} else {
			Services::Registry()->set('Route', 'home', false);
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
	public function get($catalog_id = 0, $sef_request = '', $source_id = 0, $catalog_type_id = 0)
	{
		if ((int)$catalog_id > 0) {
			$catalog_id = (int)$catalog_id;

		} else if ((int)$source_id > 0 && (int)$catalog_type_id > 0) {
			$catalog_id = $this->getID((int)$catalog_type_id, (int)$source_id);

		} else {
			$catalog_id = $this->getIDUsingSEFURL($sef_request);
		}

		$m = Services::Model()->connect('Catalog');

		$m->model->set('id', (int)$catalog_id);
		$m->model->set('get_item_children', false);
		$m->model->set('get_special_fields', 0);
		$m->model->set('add_acl_check', false);

		$row = $m->execute('load');

		if (count($row) == 0) {
			return array();
		}

		$row['request'] = 'index.php?id=' . (int)$row['id'];

		if ($row['id'] == Services::Registry()->get('Configuration', 'home_catalog_id', 0)) {
			if ($catalog_id == 0) {

			} else {
				$row->redirect_to_id =
					Services::Registry()->get('Configuration', 'home_catalog_id', 0);
			}
		}

		return $row;
	}


	/**
	 * getIDUsingSEFURL - Retrieves Catalog ID
	 *
	 * @param  null $catalog_type_id
	 * @param  null $source_id
	 *
	 * @return bool|mixed
	 * @since  1.0
	 */
	public function getIDUsingSEFURL($sef_request)
	{
		$m = Services::Model()->connect('Catalog');

		$m->model->query->select($m->model->db->qn('id'));
		$m->model->query->where($m->model->db->qn('sef_request') . ' = ' . $m->model->db->q($sef_request));

		return $m->execute('loadResult');
	}

	/**
	 * getID - Retrieves Catalog ID
	 *
	 * @param  null $catalog_type_id
	 * @param  null $source_id
	 *
	 * @return bool|mixed
	 * @since  1.0
	 */
	public function getID($catalog_type_id, $source_id)
	{
		$m = Services::Model()->connect('Catalog');

		$m->model->query->select($m->model->db->qn('id') . ' as catalog_id');
		$m->model->query->where($m->model->db->qn('catalog_type_id') . ' = ' . (int)$catalog_type_id);
		$m->model->query->where($m->model->db->qn('source_id') . ' = ' . (int)$source_id);

		return $m->execute('loadResult');
	}

	/**
	 * getRedirectURL Function retrieves the Redirect URL for the specified catalog id
	 *
	 * @param  integer $catalog_id
	 *
	 * @param  string URL
	 * @since  1.0
	 */
	public function getRedirectURL($catalog_id)
	{
		$m = Services::Model()->connect('Catalog');
		$m->model->query->select($m->model->db->qn('redirect_to_id'));
		$result = $m->execute('loadResult');

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
		if ($catalog_id == Services::Registry()->get('Configuration', 'home_catalog_id', 0)) {
			return '';
		}

		if (Services::Registry()->get('Configuration', 'sef', 1) == 1) {
			$m = Services::Model()->connect('Catalog');
			$m->model->query->select($m->model->db->qn('sef_request'));
			$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$catalog_id);
			$url = $m->execute('loadResult');
		} else {
			$url = 'index.php?id=' . (int)$catalog_id;
		}

		return $url;
	}
}
