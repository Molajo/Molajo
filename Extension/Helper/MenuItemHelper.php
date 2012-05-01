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
