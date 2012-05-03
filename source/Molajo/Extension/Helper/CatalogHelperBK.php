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
	 * Class constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{

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
var_dump($row);
        die;
		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0 || (int)$row->routable == 0) {
			Services::Registry()->set('Route', 'status_found', false);
			return false;
		}

		/** Redirect: routeRequest handles rerouting the request */
		if ((int)$row->redirect_to_id == 0) {
		} else {
			Services::Registry()->set('Route', 'redirect_to_id', (int)$row->redirect_to_id);
			return false;
		}

		/** Catalog Registry */
		Services::Registry()->set('Route', 'id', (int)$row->id);
		Services::Registry()->set('Route', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Route', 'catalog_type', $row->catalog_type);
		Services::Registry()->set('Route', 'source_table', $row->source_table);
		Services::Registry()->set('Route', 'source_id', (int)$row->source_id);
		Services::Registry()->set('Route', 'primary_category_id', (int)$row->primary_category_id);
		Services::Registry()->set('Route', 'sef_request', $row->sef_request);
		Services::Registry()->set('Route', 'request', $row->request);
		Services::Registry()->set('Route', 'redirect_to_id', (int)$row->redirect_to_id);
		Services::Registry()->set('Route', 'routable', (int)$row->routable);
		Services::Registry()->set('Route', 'view_group_id', (int)$row->view_group_id);

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
	public function get($catalog_id = 0, $sef_request = null, $source_id = 0)
	{
		$parameter_url = 'index.php?id=' . (int)$catalog_id;

		$m = Services::Model()->connect('Catalog');

		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->select($m->model->db->qn('a.catalog_type_id'));
		$m->model->query->select($m->model->db->qn('a.source_id'));
		$m->model->query->select($m->model->db->qn('a.routable'));
		$m->model->query->select($m->model->db->qn('a.sef_request'));
		$m->model->query->select($m->model->db->qn('a.redirect_to_id'));
		$m->model->query->select($m->model->db->qn('a.view_group_id'));
		$m->model->query->select($m->model->db->qn('a.primary_category_id'));

		$m->model->query->select($m->model->db->qn('b.source_table'));
		$m->model->query->select($m->model->db->qn('b.title'). ' as catalog_type');

		$m->model->query->from($m->model->db->qn('#__catalog') . ' as a');
		$m->model->query->from($m->model->db->qn('#__catalog_types') . ' as b');

		$m->model->query->where($m->model->db->qn('a.catalog_type_id')
			. ' = ' . $m->model->db->qn('b.id'));

		if ((int)$catalog_id > 0) {
			$m->model->query->where($m->model->db->qn('a.id') . ' = ' . (int)$catalog_id);

		} else if ((int)$source_id > 0) {
			$m->model->query->where($m->model->db->qn('a.source_id') . ' = ' . (int)$source_id);
			$m->model->query->where($m->model->db->qn('a.redirect_id') . ' = 0 ');

		} else if (trim($sef_request) == '') {
			$m->model->query->where($m->model->db->qn('a.id')
				. ' = ' . (int)Services::Registry()->get('Configuration', 'home_catalog_id', 0));

		} else {
			$m->model->query->where(
				'(' . $m->model->db->qn('a.sef_request') . ' = ' . $m->model->db->q($sef_request) .
					' OR' . $m->model->db->qn('a.id') . ' = ' . $m->model->db->q($catalog_id) . ')'
			);
		}

		$row = $m->execute('loadObject');

		if (count($row) == 0) {
			return array();
		}

		$row->request = 'index.php?id=' . (int)$row->id;

		if ((int)$source_id > 0) {

		} else if ((int)$catalog_id == 0) {

		} else {

			if (Services::Registry()->get('Configuration', 'sef', 1) == 1) {

				if ($row->id == $catalog_id
				) {
				} else {
					$row->redirect_to_id = (int)$row->id;
				}

			} else {
				if ($row->id == $catalog_id) {

				} else {
					$row->redirect_to_id = (int)$row->id;
				}
			}

			if ($row->id == Services::Registry()->get('Configuration', 'home_catalog_id', 0)) {
				if ($catalog_id == 0) {

				} else {
					$row->redirect_to_id =
						Services::Registry()->get('Configuration', 'home_catalog_id', 0);
				}
			}
		}

		return $row;
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
		$m->model->query->where($m->model->db->qn('view_group_id')
			. ' IN (' . implode(',', Services::Registry()->get('User', 'ViewGroups')) . ')');

		return $m->execute('loadResult');
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

		$m = Services::Model()->connect('Catalog');

		if (Services::Registry()->get('Configuration', 'sef', 1) == 1) {
			$m->model->query->select($m->model->db->qn('sef_request'));
		} else {
			$m->model->query->select($m->model->db->qn('id'));
		}

		$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$catalog_id);
		$m->model->query->where($m->model->db->qn('view_group_id') . ' IN (' .
				implode(',', Services::Registry()->get('User', 'ViewGroups')) . ')'
		);

		return $m->execute('loadResult');
	}

	/**
	 * getRedirectURL Function to retrieve catalog information for the Request or Catalog ID
	 *
	 * @param  integer $catalog_id
	 *
	 * @param  string URL
	 * @since  1.0
	 */
	public function getRedirectURL($catalog_id)
	{
		if ((int)$catalog_id == Services::Registry()->get('Configuration', 'home_catalog_id', 0)) {
			return '';
		}

		$m = Services::Model()->connect('Catalog');

		if (Services::Registry()->get('Configuration', 'sef', 1) == 0) {
			$m->model->query->select($m->model->db->qn('sef_request'));
		} else {
			$m->model->query->select($m->model->db->qn('id'));
		}

		$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$catalog_id);

		return $m->execute('loadResult');
	}
}
