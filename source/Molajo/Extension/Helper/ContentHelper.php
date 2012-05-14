<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Application;
use Molajo\Service\Services;
use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * Content Helper
 *
 * @package      Molajo
 * @subpackage   Helper
 * @since        1.0
 */
Class ContentHelper
{
	/**
	 * Static instance
	 *
	 * @var     object
	 * @since   1.0
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
			self::$instance = new ContentHelper();
		}
		return self::$instance;
	}

	/**
	 * Retrieve Route information for a specific Content Item
	 * identified in the Catalog as the request
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	public function getRoute()
	{
		/** Retrieve the query results */
		$row = $this->get(
			Services::Registry()->get('Route', 'source_id'),
			Services::Registry()->get('Route', 'source_table')
		);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			return Services::Registry()->set('Route', 'status_found', false);
		}

		Services::Registry()->set('Content', 'id', (int)$row['id']);
		Services::Registry()->set('Content', 'extension_instance_id', (int)$row['extension_instance_id']);
		Services::Registry()->set('Content', 'extension_catalog_type_id', (int)$row['extension_catalog_type_id']);
		Services::Registry()->set('Content', 'title', $row['title']);
		Services::Registry()->set('Content', 'translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Content', 'language', $row['language']);
		Services::Registry()->set('Content', 'catalog_id', $row['catalog_id']);
		Services::Registry()->set('Content', 'catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Content', 'catalog_type_title', $row['catalog_type_title']);
		Services::Registry()->set('Content', 'modified_datetime', $row['modified_datetime']);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');
		foreach ($customFieldTypes as $customFieldName) {

			Services::Registry()->deleteRegistry('Content'. ucfirst(strtolower($customFieldName)));

			Services::Registry()->copy(
				$row['model_name']. ucfirst(strtolower($customFieldName)),
				'Content'. ucfirst(strtolower($customFieldName))
			);

			Services::Registry()->deleteRegistry($row['model_name']. ucfirst(strtolower($customFieldName)));
		}

		/**
		echo '<pre>';
		var_dump(Services::Registry()->get('Content'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('ContentCustomfields'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('ContentParameters'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('ContentMetadata'));
		echo '</pre>';
		*/

		return true;
	}

	/**
	 * Retrieve Route information for a specific Category
	 * identified in the as the request
	 *
	 * @return    boolean
	 * @since    1.0
	 */
	public function getRouteCategory()
	{
		/** Retrieve the query results */
		$row = $this->get(
			Services::Registry()->get('Route', 'category_id'),
			'#__content'
		);

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			return Services::Registry()->set('Route', 'status_found', false);
		}

		Services::Registry()->set('Category', 'id', (int)$row['id']);
		Services::Registry()->set('Category', 'extension_instance_id', (int)$row['extension_instance_id']);
		Services::Registry()->set('Category', 'extension_catalog_type_id', (int)$row['extension_catalog_type_id']);
		Services::Registry()->set('Category', 'title', $row['title']);
		Services::Registry()->set('Category', 'translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Category', 'language', $row['language']);
		Services::Registry()->set('Category', 'catalog_id', $row['catalog_id']);
		Services::Registry()->set('Category', 'catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Category', 'catalog_type_title', $row['catalog_type_title']);
		Services::Registry()->set('Category', 'modified_datetime', $row['modified_datetime']);

		/** Process each field namespace  */
		$customFieldTypes = Services::Registry()->get($row['table_registry_name'], 'CustomFieldGroups');
		foreach ($customFieldTypes as $customFieldName) {

			Services::Registry()->deleteRegistry('Category'. ucfirst(strtolower($customFieldName)));

			Services::Registry()->copy(
				$row['model_name']. ucfirst(strtolower($customFieldName)),
				'Category'. ucfirst(strtolower($customFieldName))
			);

			Services::Registry()->deleteRegistry($row['model_name']. ucfirst(strtolower($customFieldName)));
		}
/**
		echo '<pre>';
		var_dump(Services::Registry()->get('Category'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('CategoryCustomfields'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('CategoryParameters'));
		echo '</pre>';
		echo '<pre>';
		var_dump(Services::Registry()->get('CategoryMetadata'));
		echo '</pre>';
*/

		return true;
	}

	/**
	 * Get data for content
	 *
	 * @return  mixed    An object containing an array of data
	 * @since   1.0
	 */
	public function get($id, $content_table)
	{
		$m = Application::Controller()->connect(
			ucfirst(strtolower(Services::Registry()->get('Route', 'catalog_type')))
		);

		$m->model->set('id', (int)$id);

		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->select($m->model->db->qn('a.catalog_type_id'));
		$m->model->query->select($m->model->db->qn('a.extension_instance_id'));
		$m->model->query->select($m->model->db->qn('a.title'));
		$m->model->query->select($m->model->db->qn('a.translation_of_id'));
		$m->model->query->select($m->model->db->qn('a.language'));
		$m->model->query->select($m->model->db->qn('a.metadata'));
		$m->model->query->select($m->model->db->qn('a.parameters'));
		$m->model->query->select($m->model->db->qn('a.modified_datetime'));

		$m->model->query->from($m->model->db->qn($content_table) . ' as a ');

		$m->model->query->where($m->model->db->qn('a.id') . ' = ' . (int)$id);

		$m->model->query->where($m->model->db->qn('a.status') . ' > ' . STATUS_UNPUBLISHED);
		$m->model->query->where('(a.start_publishing_datetime = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR a.start_publishing_datetime <= ' .
				$m->model->db->q($m->model->now) . ')'
		);
		$m->model->query->where('(a.stop_publishing_datetime = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR a.stop_publishing_datetime >= ' .
				$m->model->db->q($m->model->now) . ')'
		);

		$m->model->query->select($m->model->db->qn('b.title') . ' as catalog_type_title');
		$m->model->query->select($m->model->db->qn('b.source_table'));
		$m->model->query->from($m->model->db->qn('#__catalog_types') . ' as b ');
		$m->model->query->where($m->model->db->qn('b.id') . ' = ' . $m->model->db->qn('a.catalog_type_id'));

		$m->model->query->select($m->model->db->qn('c.catalog_type_id') . ' as extension_catalog_type_id');
		$m->model->query->from($m->model->db->qn('#__extension_instances') . ' as c ');
		$m->model->query->where($m->model->db->qn('c.id') . ' = ' . $m->model->db->qn('a.extension_instance_id'));

		/**
		 *  Run Query
		 */
		$row = $m->getData('load');

		$row['table_registry_name'] = $m->model->table_registry_name;
		$row['model_name'] = $m->model->model_name;

		if (count($row) == 0) {
			return array();
		}

		return $row;
	}
}
