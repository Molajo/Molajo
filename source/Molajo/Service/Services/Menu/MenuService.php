<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Service\Services\Menu;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Menu
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class MenuService
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
            self::$instance = new MenuService();
        }

        return self::$instance;
    }

    /**
     * Retrieve requested menu, format data, build link, verify ACL
     *
     * @param int $extension_instance_id - menu
     * @param int $start_lvl             layer within a menu
     * @param int $end_lvl               layer within a menu
     * @param int $parent_id
     * @param int $catalog_type_id
     *
     * CATALOG_TYPE_MENU_ITEM_RESOURCE
     * CATALOG_TYPE_MENU_ITEM_LINK
     * CATALOG_TYPE_MENU_ITEM_TEMPLATE_VIEW
     * CATALOG_TYPE_MENU_ITEM_SEPARATOR
     *
     * Range:
     * CATALOG_TYPE_MENU_ITEM_BEGIN
     * CATALOG_TYPE_MENU_ITEM_END
     *
     * @return array|bool
     * @since   1.0
     */
    public function runMenuQuery(
        $extension_instance_id, $start_lvl = 0, $end_lvl = 0, $parent_id = 0, $active_catalog_ids = array())
	{
        if ($extension_instance_id == 0) {
            return false;
        }

        /** Query Connection */
        $controllerClass = 'Molajo\\Controller\\DisplayController';
        $m = new $controllerClass();

        $results = $m->connect('Table', 'Menuitem');
        if ($results == false) {
            return false;
        }

        /** Select */
        $m->model->query->select($m->model->db->qn('a.id'));
        $m->model->query->select($m->model->db->qn('a.catalog_type_id'));
        $m->model->query->select($m->model->db->qn('a.title'));
        $m->model->query->select($m->model->db->qn('a.subtitle'));
        $m->model->query->select($m->model->db->qn('a.path'));
        $m->model->query->select($m->model->db->qn('a.alias'));
        $m->model->query->select($m->model->db->qn('a.root'));
        $m->model->query->select($m->model->db->qn('a.parent_id'));
        $m->model->query->select($m->model->db->qn('a.lft'));
        $m->model->query->select($m->model->db->qn('a.rgt'));
        $m->model->query->select($m->model->db->qn('a.home'));
        $m->model->query->select($m->model->db->qn('a.parameters'));
        $m->model->query->select($m->model->db->qn('a.ordering'));

        /** Set Criteria */
        if ($end_lvl == 0) {
            $end_lvl = 999999;
        }
        if ($start_lvl == 0 && $end_lvl == 999999) {
        } else {
            $m->model->query->where($m->model->db->qn('a.lvl') . ' >= ' . (int) $start_lvl
                . ' AND ' . $m->model->db->qn('a.lvl') . ' <= ' . (int) $end_lvl);
        }

        $m->model->query->where($m->model->db->qn('a.extension_instance_id') . ' = ' . (int) $extension_instance_id);

        if ((int) $parent_id == 0) {
        } else {
            $m->model->query->where($m->model->db->qn('a.parent_id') . ' = ' . (int) $parent_id);
        }

        $m->model->query->order('a.ordering');

        $m->set('model_offset', 0);
        $m->set('model_count', 999999);

        /** Execute query */
        $query_results = $m->getData('list');

		if ($query_results === false) {
            return array();
        }

        /** Add in URL */
        foreach ($query_results as $item) {

            if ($item->catalog_type_id == CATALOG_TYPE_MENU_ITEM_RESOURCE) {

                if (in_array($item->catalog_id, $active_catalog_ids)) {
                    $item->css_class = 'active';
                } else {
                    $item->css_class = '';
                }

                if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
                    $item->url = Services::Url()->getApplicationURL($item->catalog_sef_request);
                } else {
                    $item->url = Services::Url()->getApplicationURL('index.php?id=' . (int) $item->id);
                }

                if ($item->subtitle == ''
					|| $item->subtitle == null
				) {
                    $item->link_text = $item->title;
                } else {
                    $item->link_text = $item->subtitle;
                }

                $item->link = $item->url;
            }
        }

        return $query_results;
    }

    /**
     * Retrieves an array of active menuitems, including the current menuitem and its parents
     *
     * @param int $current_menuitem_id - menu
     *
     * @return array|bool
     * @since   1.0
     */
    public function getMenuBreadcrumbIds($current_menuitem_id)
    {

        if ($current_menuitem_id == 0) {
            return false;
        }

        /** Query Connection */
        $controllerClass = 'Molajo\\Controller\\DisplayController';
        $m = new $controllerClass();

        $results = $m->connect('Table', 'MenuitemsNested');
        if ($results == false) {
            return false;
        }

        $m->model->query->where($m->model->db->qn('current_menuitem.id') . ' = ' . (int) $current_menuitem_id);

        $m->model->query->order('a.lft');

        $m->set('model_offset', 0);
        $m->set('model_count', 999999);

        /** Execute query */
        $query_results = $m->getData('list');

		/**
		echo '<br /><br />';
		echo $m->model->query->__toString();
		echo '<br /><br />';

		echo '<pre>';
		var_dump($query_results);
		echo '</pre>';
		*/
        /** Add in URL */
        foreach ($query_results as $item) {

            if ($item->catalog_type_id == CATALOG_TYPE_MENU_ITEM_RESOURCE) {
                if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
                    $item->url = Services::Url()->getApplicationURL($item->catalog_sef_request);
                } else {
                    $item->url = Services::Url()->getApplicationURL('index.php?id=' . (int) $item->id);
                }
            }
        }

        return $query_results;
    }
}
