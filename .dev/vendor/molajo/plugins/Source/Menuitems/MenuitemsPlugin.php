<?php
/**
 * Menuitems Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Menuitems;

use CommonApi\Event\SystemInterface;
use Molajo\Plugins\SystemEventPlugin;
use stdClass;

/**
 * Menuitems Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class MenuitemsPlugin extends SystemEventPlugin implements SystemInterface
{
    /**
     * Generates list of Menus and Menuitems for use in Datalists
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeExecute()
    {
        $controller = $this->setMenuitemsQueryObject();

        $results = $this->runQuery($controller);

        if (count($results) === 0) {
            $this->plugin_data->datalists->menuitems = array();
            return $this;
        }

        $menuitems = $this->processItems($results);

        $this->plugin_data->datalists->menuitems = $menuitems;

        return $this;
    }

    /**
     * Process Query Results
     *
     * @param   array $results
     *
     * @return  array
     * @since   1.0
     */
    public function processItems($results)
    {
        $menuitems = array();
        foreach ($results as $item) {
            $menuitems[] = $this->processItem($item);
        }

        return $menuitems;
    }

    /**
     * Process Item
     *
     * @param   object $item
     *
     * @return  stdClass
     * @since   1.0
     */
    public function processItem($item)
    {
        $temp_row = new stdClass();

        $name = $item->title;
        $lvl  = (int)$item->lvl - 1;
        $name = $this->setLevelDots($lvl, $name);

        $temp_row->id    = $item->id;
        $temp_row->value = trim($name);

        return $temp_row;
    }

    /**
     * Set Menuitems Query
     *
     * @return   object
     * @since    1.0.0
     */
    protected function setMenuitemsQueryObject()
    {
        $controller = $this->resource->get('query:///Molajo//Model//Datasource//Menuitem.xml');

        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('use_special_joins', 1);

        return $this->setMenuitemsQuery($controller);
    }

    /**
     * Set Menuitems Query
     *
     * @param   object  $controller
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setMenuitemsQuery($controller)
    {
        $prefix  = $controller->getModelRegistry('primary_prefix', 'a');
        $catalog = (int)$this->runtime_data->reference_data->catalog_type_menuitem_id;

        $controller->select($prefix . '.' . 'title');
        $controller->select($prefix . '.' . 'id');
        $controller->select($prefix . '.' . 'lvl');
        $controller->where('column', $prefix . '.' . 'status', 'IN', 'integer', '0,1,2');
        $controller->where('column', 'catalog' . '.' . 'enabled', '=', 'integer', '1');
        $controller->where('column', $prefix . '.' . 'catalog_type_id', '=', 'integer', $catalog);
        $controller->orderBy($prefix . '.' . 'root', 'ASC');
        $controller->orderBy($prefix . '.' . 'lft', 'ASC');
        $controller->setModelRegistry('model_use_pagination', 0);

        return $controller;
    }
}
