<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */
namespace Molajo\Extension\Helper;

use Molajo\Service\Services;

use Molajo\Extension\Helpers;

defined('MOLAJO') or die;

/**
 * ContentHelper
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
	 * Class constructor.
	 *
	 * @since  1.0
	 */
	public function __construct()
	{

	}

	/**
	 * Retrieve Route information for a specific Menu Item
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
		Services::Registry()->set('Content', 'catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Content', 'extension_instance_id', (int)$row['extension_instance_id']);
		Services::Registry()->set('Content', 'title', $row['title']);
		Services::Registry()->set('Content', 'translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Content', 'language', (string)$row['language']);

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
			Services::Registry()->get('Route', 'primary_category_id'),
			'#__content'
		);
echo '<pre>Category';
var_dump($row);
die;
		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			return Services::Registry()->set('Route', 'status_found', false);
		}

		Services::Registry()->set('Category', 'id', (int)$row['id']);
		Services::Registry()->set('Category', 'catalog_type_id', (int)$row['catalog_type_id']);
		Services::Registry()->set('Category', 'title', $row['title']);
		Services::Registry()->set('Category', 'translation_of_id', (int)$row['translation_of_id']);
		Services::Registry()->set('Category', 'language', (string)$row['language']);

		$xml = Services::Registry()->loadFile('Category', 'Table');

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
		$m = Services::Model()->connect(
			ucfirst(strtolower(Services::Registry()->get('Route', 'catalog_type')))
		);

		$m->model->set('id', (int)$id);

		$m->model->set('get_special_fields', true);
		$m->model->set('get_item_children', false);
		$m->model->set('use_special_joins', false);
		$m->model->set('add_acl_check', true);

		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->select($m->model->db->qn('a.catalog_type_id'));
		$m->model->query->select($m->model->db->qn('a.extension_instance_id'));
		$m->model->query->select($m->model->db->qn('a.title'));
		$m->model->query->select($m->model->db->qn('a.translation_of_id'));
		$m->model->query->select($m->model->db->qn('a.language'));
		$m->model->query->select($m->model->db->qn('a.metadata'));
		$m->model->query->select($m->model->db->qn('a.parameters'));

        $m->model->query->from($m->model->db->qn($content_table) . ' as a ');

        $m->model->query->where('a.' . $m->model->db->qn('id') . ' = ' . (int)$id);
        $m->model->query->where('a.' . $m->model->db->qn('status') .
            ' > ' . STATUS_UNPUBLISHED);

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

		/**
		 *  Run Query
		 */
		$row = $m->execute('load');

		if (count($row) == 0) {
			return array();
		}

		return $row;
    }
}
