<?php
/**
 * Page Type Grid Plugin Base
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypegrid;

use Molajo\Plugins\DisplayEvent;
use stdClass;

/**
 * Page Type Grid Plugin Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Base extends DisplayEvent
{
    /**
     * Get Data for Grid Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function initialiseClass()
    {
        $this->plugin_data->form_select_list = array();
        $this->plugin_data->grid             = new stdClass();

        $this->getGridColumns();

        return $this;
    }

    /**
     * Get Grid Columns
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridColumns()
    {
        $parameters       = $this->runtime_data->resource->parameters;
        $grid_column_list = array();

        for ($i = 1; $i < 16; $i++) {
            $grid_column_list = $this->getGridColumnsItem($grid_column_list, $i, $parameters);
        }

        $this->plugin_data->grid_columns = $grid_column_list;

        return $this;
    }

    /**
     * Get Grid Column Item
     *
     * @param   array   $grid_column_list
     * @param   integer $i
     * @param   array   $parameters
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getGridColumnsItem($grid_column_list, $i, $parameters)
    {
        $grid_column_number = 'grid_column' . $i;

        if (isset($parameters->$grid_column_number)) {

            $field = trim($parameters->$grid_column_number);

            if (trim($field) === '') {
            } else {
                $grid_column_list[] = $field;
            }
        }

        return $grid_column_list;
    }
}
