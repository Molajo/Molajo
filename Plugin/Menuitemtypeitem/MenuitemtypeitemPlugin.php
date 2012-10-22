<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Menuitemtypeitem;

use Molajo\Plugin\Plugin\Plugin;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MenuitemtypeitemPlugin extends Plugin
{
    /**
     * Prepares data for Menuitemtypeitem
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('catalog_menuitem_type')) == 'item') {
        } else {
            return true;
        }

        $resource_table_registry = ucfirst(strtolower($this->get('model_name')))
            . ucfirst(strtolower($this->get('model_type')));

        /** Get Actual Data for matching to Fields
        $controllerClass = 'Molajo\\MVC\\Controller\\Controller';
        $connect = new $controllerClass();
        $results = $connect->connect($this->get('model_type'), $this->get('model_name'));
        if ($results === false) {
            return false;
        }

        $connect->set('get_customfields', 1);
        $connect->set('use_special_joins', 1);
        $connect->set('process_plugins', 1);
        $primary_prefix = $connect->get('primary_prefix');
        $primary_key = $connect->get('primary_key');
        $id = $this->get('criteria_source_id');

        $connect->model->query->where($connect->model->db->qn($primary_prefix)
                . '.' . $connect->model->db->qn($primary_key) . ' = ' . (int) $id);

        $item = $connect->getData('item');

        /** PrimaryRequestQueryResults populated in Content Helper getRouteMenuitemtypeitem */

        $this->set('model_name', 'Plugindata');
        $this->set('model_type', 'dbo');
        $this->set('model_query_object', 'getPlugindata');
        $this->set('model_parameter', 'PrimaryRequestQueryResults');

        $this->parameters['model_name'] = 'Plugindata';
        $this->parameters['model_type'] = 'dbo';

        return true;
    }
}
