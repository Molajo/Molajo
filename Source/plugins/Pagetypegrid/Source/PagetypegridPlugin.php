<?php
/**
 * Page Type Grid Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypegrid;

use CommonApi\Event\DisplayEventInterface;
use stdClass;

/**
 * Page Type Grid Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class PagetypegridPlugin extends GridQuery implements DisplayEventInterface
{
    /**
     * List Names
     *
     * @var    array
     * @since  1.0.0
     */
    protected $list_name_array = array();

    /**
     * Build Lists
     *
     * @var    array
     * @since  1.0.0
     */
    protected $build_list_array = array();

    /**
     * Before Render
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->setGrid();

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
        if (strtolower($this->runtime_data->route->page_type) === 'grid') {
            return true;
        }

        return false;
    }

    /**
     * Get Data for Grid Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setGrid()
    {
        $this->getFieldLists($this->runtime_data->resource->model_registry);
        $this->initialiseClass();
        $this->getGridData();
        $this->setGridFilter();
        $this->setGridFieldFilter();
        $this->setFirstLastEvenOdd();
        $this->setBatch();
        $this->setPluginDataFormBeginValues('PUT', strtolower($this->runtime_data->route->page_type));

        return $this;
    }

    /**
     * Filters: lists stored in registry, where clauses for primary grid query set
     *
     * @return  $this
     * @since   1.0.0
     * @throws  /CommonApi/Exception/RuntimeException
     */
    protected function setGridFilter()
    {
        for ($i = 1; $i < 11; $i++) {
            $this->getGridFilterList($i, $this->runtime_data->resource->parameters);
        }

        $this->plugin_data->grid_filters = $this->list_name_array;

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        $list = $this->getDataLists($this->build_list_array, $options);

        if (count($list) > 0) {
            foreach ($list as $list_name => $list_array) {
                $list_name                     = strtolower($list_name);
                $this->plugin_data->$list_name = $list_array;
            }
        }

        return $this;
    }

    /**
     * Determine lists to build
     *
     * @param   integer $i
     * @param   object  $parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridFilterList($i, $parameters)
    {
        /** Does Parameter Field exist? */
        $grid_list_number = 'grid_filter_list' . $i;
        if (isset($parameters->$grid_list_number)) {
        } else {
            return $this;
        }

        /** Does Parameter Field have a value? */
        $list_name = trim($parameters->$grid_list_number);
        if (trim($list_name) === '' || $list_name === null) {
            return $this;
        }

        /** Has the list already been identified? */
        if (in_array($list_name, $this->list_name_array)) {
            return $this;
        }

        /** Valid List */
        $temp                    = new stdClass();
        $temp->list_name         = $list_name;
        $this->list_name_array[] = $temp;

        /** Has the list already been built? */
        $temp_name = 'datalist_' . strtolower($list_name) . trim(strtolower($parameters->name));
        if (isset($this->plugin_data->$temp_name)) {
        } else {
            $this->build_list_array[] = $list_name;
        }

        return $this;
    }

    /**
     * Fields used by resource
     *
     * @return  $this
     * @since   1.0.0
     * @throws  /CommonApi/Exception/RuntimeException
     */
    protected function setGridFieldFilter()
    {
        $first      = 1;
        $temp_array = array();

        if (is_array($this->plugin_data->fields)
            && count($this->plugin_data->fields) > 0
        ) {

            foreach ($this->plugin_data->fields as $field) {
                $temp               = new stdClass();
                $temp->id           = $field->id;
                $temp->value        = $field->value;
                $temp->multiple     = '';
                $temp->size         = '';
                $temp->selected     = '';
                $temp->no_selection = 1;
                $temp->first        = $first;
                $temp->list_name    = $this->language->translateString('Fields');
                $temp_array[]       = $temp;
                $first              = 0;
            }
        }

        $this->plugin_data->grid_fields = $temp_array;

        return $this;
    }

    /**
     * First, Even/Odd and Last Rows
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setFirstLastEvenOdd()
    {
        $rows = $this->plugin_data->grid_data;

        $total_rows      = count($rows);
        $even_or_odd_row = 'odd ';
        $count           = 0;

        if ($total_rows === 0) {
        } else {
            foreach ($rows as $row) {
                $count++;

                if ($count === 1) {
                    $row->first_row = 'first ';
                } else {
                    $row->first_row = '';
                }

                $row->even_or_odd_row = $even_or_odd_row;
                if ($even_or_odd_row === 'odd ') {
                    $even_or_odd_row = 'even ';
                } else {
                    $even_or_odd_row = 'odd ';
                }

                $row->total_rows = $total_rows;

                if ($total_rows === $count) {
                    $row->last_row = 'last';
                } else {
                    $row->last_row = '';
                }

                $row->grid_row_class = trim(
                    trim($row->first_row)
                    . ' ' . trim($row->even_or_odd_row)
                    . ' ' . trim($row->last_row)
                );
            }
        }

        $this->plugin_data->grid_data = $rows;

        return $this;
    }

    /**
     * Creates and stores lists for Grid Batch area
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setBatch()
    {
        $parameters = $this->runtime_data->resource->parameters;

        $this->plugin_data->grid_batch_categories = array();
        $this->plugin_data->grid_batch_tags       = array();
        $this->plugin_data->grid_batch_groups     = array();

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        if ((int)$parameters->grid_batch_categories === 1) {
            $this->plugin_data->grid_batch_categories = $this->getDatalist('Categories', $options);
        }
        if ((int)$parameters->grid_batch_tags === 1) {
            $this->plugin_data->grid_batch_tags = $this->getDatalist('Tags', $options);
        }
        if ((int)$parameters->grid_batch_permissions === 1) {
            $this->plugin_data->grid_batch_groups = $this->getDatalist('Groups', $options);
        }

        return $this;
    }
}
