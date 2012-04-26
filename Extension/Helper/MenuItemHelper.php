<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License version 2 or later; see LICENSE
 */

namespace Molajo\Extension\Helper;

use Molajo\MVC\Model\TableModel;

use Molajo\Service;

defined('MOLAJO') or die;

/**
 * Menuitem
 *
 * @package   Molajo
 * @subpackage  Helper
 * @since       1.0
 */
abstract class MenuitemHelper
{
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
    public static function get($menu_item_id)
    {
		$m = new TableModel('Content');

        /**
         *  a. Content Table
         *      Menu Items
         */
        $m->query->select('a.' . $m->db->qn('id'));
        $m->query->select('a.' . $m->db->qn('catalog_type_id'));
        $m->query->select('a.' . $m->db->qn('title'));
		$m->query->select('a.' . $m->db->qn('alias'));
		$m->query->select('a.' . $m->db->qn('path'));
        $m->query->select('a.' . $m->db->qn('custom_fields'));
        $m->query->select('a.' . $m->db->qn('parameters'));
        $m->query->select('a.' . $m->db->qn('metadata'));
        $m->query->select('a.' . $m->db->qn('translation_of_id'));
        $m->query->select('a.' . $m->db->qn('language'));

        $m->query->select('a_catalog.' . $m->db->qn('id') . ' as catalog_id');
        $m->query->select('a_catalog.' . $m->db->qn('view_group_id') . ' as view_group_id');

        $m->query->from($m->db->qn('#__content') . ' as a');

        $m->query->where('a.' . $m->db->qn('extension_instance_id') .
            ' = b.' . $m->db->qn('id'));
        $m->query->where('a.' . $m->db->qn('id') . ' = ' . (int)$menu_item_id);

        /** Catalog Join and View Access Check */
        Service::Access()->setQueryViewAccess(
            $m->query,
            $m->db,
            array('join_to_prefix' => 'a',
                'join_to_primary_key' => 'id',
                'catalog_prefix' => 'a_catalog',
                'select' => false
            )
        );

        /**
         *  b. Extensions Instances Table
         */
        $m->query->select('b.' . $m->db->qn('id') . ' as menu_id');
        $m->query->select('b.' . $m->db->qn('catalog_type_id') . 'as menu_catalog_type_id');
        $m->query->select('b.' . $m->db->qn('title') . ' as menu_title');
        $m->query->select('b.' . $m->db->qn('parameters') . 'as menu_parameters');
        $m->query->select('b.' . $m->db->qn('metadata') . 'as menu_metadata');

        $m->query->select('b_catalog.' . $m->db->qn('id') . ' as menu_catalog_id');
        $m->query->select('b_catalog.' . $m->db->qn('view_group_id') . ' as menu_view_group_id');

        $m->query->from($m->db->qn('#__extension_instances') . ' as b');

        $m->query->where('b.' . $m->db->qn('status') . ' = ' . STATUS_PUBLISHED);
        $m->query->where('(b.start_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR b.start_publishing_datetime <= ' . $m->db->q($m->now) . ')'
        );
        $m->query->where('(b.stop_publishing_datetime = ' .
                $m->db->q($m->nullDate) .
                ' OR b.stop_publishing_datetime >= ' . $m->db->q($m->now) . ')'
        );

        Service::Access()->setQueryViewAccess(
            $m->query,
            $m->db,
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
        $m->query->from($m->db->qn('#__application_extension_instances') . ' as c');
        $m->query->where('c.' . $m->db->qn('extension_instance_id') .
            ' = b.' . $m->db->qn('id'));
        $m->query->where('c.' . $m->db->qn('application_id') .
            ' = ' . APPLICATION_ID);

        /**
         *  d. Site Table
         *      Extension Instances must be enabled for the Site
         */
        $m->query->from($m->db->qn('#__site_extension_instances') . ' as d');
        $m->query->where('d.' . $m->db->qn('extension_instance_id') .
            ' = b.' . $m->db->qn('id'));
        $m->query->where('d.' . $m->db->qn('site_id') .
            ' = ' . SITE_ID);

        /**
         *  Run Query
         */
		$row = $m->loadObject();

		if (count($row) == 0) {
			return array();
		}

        return $row;
    }
}
