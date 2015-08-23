<?php
/**
 * Menuitems Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Menuitems;

use CommonApi\Event\SystemEventInterface;
use Molajo\Plugins\SystemEvent;
use stdClass;

/**
 * Menuitems Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class MenuitemsPlugin extends SystemEvent implements SystemEventInterface
{
    /**
     * Generates list of Menus and Menuitems for use in Datalists
     *  $this->plugin_data->datalists->menuitems
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeExecute()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->setMenuitems();

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if ($this->runtime_data->request->client->ajax === 1) {
            return false;
        }

        return true;
    }

    /**
     * Generates list of Menus and Menuitems for use in Datalists
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setMenuitems()
    {
        $this->setMenuitemsQueryObject();

        $this->setMenuitemsQuery();

        $results = $this->runQuery();

        if (count($results) === 0) {
            $this->plugin_data->menuitems = array();
            return $this;
        }

        $menuitems = $this->processItems($results);

        $this->plugin_data->menuitems = $menuitems;

        $this->setIdAndTitle($menuitems);

        return $this;
    }

    /**
     * Process Query Results
     *
     * @param   array $menuitems
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setIdAndTitle($menuitems)
    {
        $list = array();

        foreach ($menuitems as $item) {
            $row        = new stdClass();
            $row->id    = $item->id;
            $row->value = $item->value;
            $list[]     = $row;
        }

        $this->plugin_data->datalist_menuitems = $this->getDataList('Menuitems', array('value_list' => $list));

        return $this;
    }

    /**
     * Process Query Results
     *
     * @param   array $results
     *
     * @return  array
     * @since   1.0.0
     */
    protected function processItems($results)
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
     * @since   1.0.0
     */
    protected function processItem($item)
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
     * Set Menuitems Query Object
     *
     * @return   object
     * @since    1.0.0
     */
    protected function setMenuitemsQueryObject()
    {
        $this->setQueryController('Molajo//Model//Datasource//Menuitem.xml');

        $this->setQueryControllerDefaults(
            $process_events = 0,
            $query_object = 'list',
            $get_customfields = 0,
            $use_special_joins = 1,
            $use_pagination = 0,
            $check_view_level_access = 1,
            $get_item_children = 0
        );

        return $this;
    }

    /**
     * Set Menuitems Query
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setMenuitemsQuery()
    {
        $prefix = $this->query->getModelRegistry('primary_prefix', 'a');

        $catalog = (int)$this->runtime_data->reference_data->catalog_type_menuitem_id;

        $this->query->select($prefix . '.' . 'title');
        $this->query->select($prefix . '.' . 'id');
        $this->query->select($prefix . '.' . 'lvl');

        $this->query->where('column', $prefix . '.' . 'status', 'IN', 'integer', '0,1,2');
        $this->query->where('column', 'catalog' . '.' . 'enabled', '=', 'integer', '1');
        $this->query->where('column', $prefix . '.' . 'catalog_type_id', '=', 'integer', $catalog);

        $this->query->orderBy($prefix . '.' . 'root', 'ASC');
        $this->query->orderBy($prefix . '.' . 'lft', 'ASC');

        return $this->query;
    }
}
