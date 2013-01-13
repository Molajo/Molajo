<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Menuitems;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class MenuitemsPlugin extends Plugin
{
    /**
     * Generates list of Menus and Menuitems for use in Datalists
     *
     * @return  boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $controllerClass = CONTROLLER_CLASS_NAMESPACE;
        $controller      = new $controllerClass();
        $controller->getModelRegistry('System', 'Menuitems', 1);

        $controller->set('check_view_level_access', 1, 'model_registry');
        $controller->set('model_offset', 0, 'model_registry');
        $controller->set('model_count', 999999, 'model_registry');
        $controller->set('get_customfields', 2, 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');

        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('title')
        );
        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('id')
        );
        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('lvl')
        );

        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('status')
                . ' IN (0,1,2)'
        );
        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('catalog_type_id')
                . ' = ' . CATALOG_TYPE_MENUITEM
        );

        $controller->model->query->order(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('root') . ', '
                . $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('lft')
        );

        $controller->set('model_offset', 0, 'model_registry');
        $controller->set('model_count', 99999, 'model_registry');

        $temp_query_results = $controller->getData(QUERY_OBJECT_LIST);

        $menuitems = array();
        foreach ($temp_query_results as $item) {
            $temp_row = new \stdClass();

            $name = $item->title;
            $lvl  = (int)$item->lvl - 1;

            if ($lvl > 0) {
                for ($i = 0; $i < $lvl; $i++) {
                    $name = ' ..' . $name;
                }
            }

            $temp_row->id    = $item->id;
            $temp_row->value = trim($name);

            $menuitems[] = $temp_row;
        }

        Services::Registry()->set('Datalist', 'Menuitems', $menuitems);

        return true;
    }
}
