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
 * MenuitemHelper
 *
 * @package       Molajo
 * @subpackage    Helper
 * @since         1.0
 */
Class MenuitemHelper
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
			self::$instance = new MenuitemHelper();
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
		$row = $this->get(Services::Registry()->get('Route', 'source_id'));

		/** 404: routeRequest handles redirecting to error page */
		if (count($row) == 0) {
			return Services::Registry()->set('Route', 'status_found', false);
		}

		Services::Registry()->set('Menuitem', 'id', (int)$row->id);
		Services::Registry()->set('Menuitem', 'catalog_type_id', (int)$row->catalog_type_id);
		Services::Registry()->set('Menuitem', 'title', $row->title);
		Services::Registry()->set('Menuitem', 'alias', $row->alias);
		Services::Registry()->set('Menuitem', 'path', $row->path);
		Services::Registry()->set('Menuitem', 'source_table', '#__content');
		Services::Registry()->set('Menuitem', 'view_group_id', (int)$row->view_group_id);
		Services::Registry()->set('Menuitem', 'translation_of_id', (int)$row->translation_of_id);
		Services::Registry()->set('Menuitem', 'language', (string)$row->language);
		Services::Registry()->set('Menuitem', 'catalog_id', (int)$row->catalog_id);
		Services::Registry()->set('Menuitem', 'view_group_id', (int)$row->view_group_id);
		Services::Registry()->set('Menuitem', 'menu_id', (int)$row->menu_id);
		Services::Registry()->set('Menuitem', 'menu_catalog_type_id', (int)$row->menu_catalog_type_id);
		Services::Registry()->set('Menuitem', 'menu_title', (string)$row->menu_title);
		Services::Registry()->set('Menuitem', 'menu_parameters', $row->menu_parameters);
		Services::Registry()->set('Menuitem', 'menu_metadata', (string)$row->menu_metadata);
		Services::Registry()->set('Menuitem', 'menu_catalog_id', (int)$row->menu_catalog_id);
		Services::Registry()->set('Menuitem', 'menu_view_group_id', (int)$row->menu_view_group_id);

		$xml = Services::Configuration()->loadFile('Menuitem', 'Table');

		Services::Registry()->loadField(
			'MenuitemCustomfields',
			'custom_field',
			$row->custom_fields,
			$xml->fields
		);
		Services::Registry()->loadField(
			'MenuitemMetadata',
			'meta',
			$row->metadata,
			$xml->fields
		);
		Services::Registry()->loadField(
			'MenuitemParameters',
			'parameter',
			$row->parameters,
			$xml->fields
		);

		return true;
	}

	/**
	 * get
	 *
	 * Retrieves Menu item data and verifies access for the extension instances
	 * user, application and site
	 *
	 * @param   $catalog_type_id
	 * @param   $extension
	 *
	 * @return  bool|mixed
	 * @since   1.0
	 */
	public function get($menu_item_id)
	{
		$m = Services::Model()->connect('Content');

		/**
		 *  a. Content Table
		 *      Menu Items
		 */
		$m->model->query->select($m->model->db->qn('a.id'));
		$m->model->query->select($m->model->db->qn('a.catalog_type_id'));
		$m->model->query->select($m->model->db->qn('a.title'));
		$m->model->query->select($m->model->db->qn('a.alias'));
		$m->model->query->select($m->model->db->qn('a.path'));
		$m->model->query->select($m->model->db->qn('a.custom_fields'));
		$m->model->query->select($m->model->db->qn('a.parameters'));
		$m->model->query->select($m->model->db->qn('a.metadata'));
		$m->model->query->select($m->model->db->qn('a.translation_of_id'));
		$m->model->query->select($m->model->db->qn('a.language'));

		$m->model->query->select($m->model->db->qn('a_catalog.id') . ' as catalog_id');
		$m->model->query->select($m->model->db->qn('a_catalog.view_group_id') . ' as view_group_id');

		$m->model->query->from($m->model->db->qn('#__content') . ' as a');

		$m->model->query->where($m->model->db->qn('a.extension_instance_id') .
			' = ' . $m->model->db->qn('b.id'));
		$m->model->query->where($m->model->db->qn('a.id') . ' = ' . (int)$menu_item_id);

		/** Catalog Join and View Access Check */
		Services::Authorisation()->setQueryViewAccess(
			$m->model->query,
			$m->model->db,
			array('join_to_prefix' => 'a',
				'join_to_primary_key' => 'id',
				'catalog_prefix' => 'a_catalog',
				'select' => false
			)
		);

		/**
		 *  b. Extensions Instances Table
		 */
		$m->model->query->select($m->model->db->qn('b.id') . ' as menu_id');
		$m->model->query->select($m->model->db->qn('b.catalog_type_id') . 'as menu_catalog_type_id');
		$m->model->query->select($m->model->db->qn('b.title') . ' as menu_title');
		$m->model->query->select($m->model->db->qn('b.parameters') . 'as menu_parameters');
		$m->model->query->select($m->model->db->qn('b.metadata') . 'as menu_metadata');

		$m->model->query->select($m->model->db->qn('b_catalog.id') . ' as menu_catalog_id');
		$m->model->query->select($m->model->db->qn('b_catalog.view_group_id') . ' as menu_view_group_id');

		$m->model->query->from($m->model->db->qn('#__extension_instances') . ' as b');

		$m->model->query->where($m->model->db->qn('b.status') . ' = ' . STATUS_PUBLISHED);
		$m->model->query->where('(b.start_publishing_datetime = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR b.start_publishing_datetime <= ' . $m->model->db->q($m->model->now) . ')'
		);
		$m->model->query->where('(b.stop_publishing_datetime = ' .
				$m->model->db->q($m->model->nullDate) .
				' OR b.stop_publishing_datetime >= ' . $m->model->db->q($m->model->now) . ')'
		);

		Services::Authorisation()->setQueryViewAccess(
			$m->model->query,
			$m->model->db,
			array('join_to_prefix' => 'b',
				'join_to_primary_key' => 'id',
				'catalog_prefix' => 'b_catalog',
				'select' => false
			)
		);

		/**
		 *  c. Application Table
		 *      Extension Instances must be enabled for the Application
		 */
		$m->model->query->from($m->model->db->qn('#__application_extension_instances') . ' as c');
		$m->model->query->where($m->model->db->qn('c.extension_instance_id') . ' = ' . $m->model->db->qn('b.id'));
		$m->model->query->where($m->model->db->qn('c.application_id') . ' = ' . APPLICATION_ID);

		/**
		 *  d. Site Table
		 *      Extension Instances must be enabled for the Site
		 */
		$m->model->query->from($m->model->db->qn('#__site_extension_instances') . ' as d');
		$m->model->query->where($m->model->db->qn('d.extension_instance_id') . ' = ' . $m->model->db->qn('b.id'));
		$m->model->query->where($m->model->db->qn('d.site_id') . ' = ' . SITE_ID);

		/**
		 *  Run Query
		 */
		$row = $m->execute('loadObject');

		if (count($row) == 0) {
			return array();
		}

		return $row;
	}
}
