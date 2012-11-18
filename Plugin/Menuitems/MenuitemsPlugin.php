<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Menuitems;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MenuitemsPlugin extends Plugin
{
    /**
     * Generates list of Menus and Menuitems for use in Datalists
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterRoute()
    {
        if (APPLICATION_ID == 2) {
        } else {
            return true;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry('System', 'Menuitems');
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('get_customfields', 0);
        $controller->set('use_special_joins', 0);
        $controller->set('process_plugins', 0);
        $controller->set('check_view_level_access', 0);

        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('title')
        );
        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('id')
        );
        $controller->model->query->select(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('lvl')
        );

        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('status')
                . ' IN (0,1,2)'
        );

        $controller->model->query->order(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('root') . ', '
                . $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('lft')
        );

        $controller->set('model_offset', 0);
        $controller->set('model_count', 99999);

        $query_results = $controller->getData(QUERY_OBJECT_LIST);

        $menuitems = array();
        foreach ($query_results as $item) {
            $row = new \stdClass();

            $name = $item->title;
            $lvl = (int) $item->lvl - 1;

            if ($lvl > 0) {
                for ($i = 0; $i < $lvl; $i++) {
                    $name = ' ..' . $name;
                }
            }

            $row->id = $item->id;
            $row->value = trim($name);

            $menuitems[] = $row;
        }

        Services::Registry()->set('Datalist', 'Menuitems', $menuitems);

        return true;
    }
}
