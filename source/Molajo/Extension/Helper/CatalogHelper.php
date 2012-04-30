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
 * Catalog
 *
 * @package       Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class CatalogHelper
{
	/**
	 * getCatalog - Retrieve Catalog and Catalog Type for specific id or query request
	 *
	 * View Access is verified in Application::Request to identify 403 errors
	 *
	 * @param    int  $catalog_id
	 * @param    null $sef_requst
	 *
	 * @results  object
	 * @since    1.0
	 */
	public static function get($catalog_id = 0, $sef_request = null, $source_id = 0)
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

		$m->model->query->from($m->model->db->qn('#__catalog') . ' as a');
		$m->model->query->from($m->model->db->qn('#__catalog_types') . ' as b');

		$m->model->query->where($m->model->db->qn('a.catalog_type_id')
			. ' = ' . $m->model->db->qn('b.id'));

		if ((int)$catalog_id > 0) {
			$m->model->query->where($m->model->db->qn('id') . ' = ' . (int)$catalog_id);

		} else if ((int)$source_id > 0) {
			$m->model->query->where($m->model->db->qn('source_id') . ' = ' . (int)$source_id);
			$m->model->query->where($m->model->db->qn('redirect_id') . ' = 0 ');

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
	public static function getID($catalog_type_id, $source_id)
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
	public static function getURL($catalog_id)
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
	public static function getRedirectURL($catalog_id)
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
