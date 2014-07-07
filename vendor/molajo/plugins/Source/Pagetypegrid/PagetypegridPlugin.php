<?php
/**
 * Page Type Grid Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypegrid;

use CommonApi\Event\DisplayInterface;
use CommonApi\Exception\RuntimeException;
use Exception;
use stdClass;

/**
 * Page Type Grid Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypegridPlugin extends GridQuery implements DisplayInterface
{
    /**
     * Before Render
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processPagetypegridPlugin() === false) {
            return $this;
        }

        return $this->setGrid();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processPagetypegridPlugin()
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
        $this->initialiseClass();

        $this->getGridData();
        $this->setGridFilter();
        $this->setGridFieldFilter();
        $this->setFirstLastEvenOdd();
        $this->setBatch();

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
        $parameters = $this->runtime_data->resource->menuitem->parameters;

        $grid_list = array();
        $temp      = array();

        for ($i = 1; $i < 11; $i ++) {

            $grid_list_number = 'grid_filter_list' . $i;

            if (isset($parameters->$grid_list_number)) {

                if (trim($parameters->$grid_list_number) === '') {

                } else {
                    if (in_array($grid_list_number, $temp)) {

                    } else {
                        $temp[]         = $grid_list_number;
                        $row            = new stdClass();
                        $row->list_name = $parameters->$grid_list_number;
                        $grid_list[]    = $row;
                    }
                }
            }
        }

        $class    = 'Molajo\\Controller\\Datalist';
        $datalist = new $class($this->resource);

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $row) {

                //@todo figure out selected value
                $selected = '';

                $list = $row->list_name;

                if ((string)$list === '') {
                } else {

                    if (isset($this->plugin_data->datalists->$list)) {
                        $value = $this->plugin_data->datalists->$list;
                    } else {
                        $value = $datalist->getDatalist($list, $options);
                    }

                    if (is_array($value) && count($value) > 0) {

                        usort(
                            $value,
                            function ($a, $b) {
                                return strcmp($a->value, $b->value);
                            }
                        );

                    } else {
                        $value = array();
                    }

                    $listname                     = strtolower($list);
                    $this->plugin_data->$listname = $value;
                }
            }
        }

        $this->plugin_data->grid_filters = $grid_list;

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
        if (is_array($this->plugin_data->fields)
            && count($this->plugin_data->fields) > 0
        ) {

            $first      = 1;
            $temp_array = array();

            foreach ($this->plugin_data->fields as $field) {

                $temp               = new stdClass();
                $temp->id           = $field->id;
                $temp->value        = $field->value;
                $temp->multiple     = '';
                $temp->size         = '';
                $temp->selected     = '';
                $temp->no_selection = 1;
                $temp->first        = $first;
                $temp->list_name    = $this->language_controller->translateString('Fields');
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
                $count ++;

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
        $parameters = $this->runtime_data->resource->menuitem->parameters;

        $class    = 'Molajo\\Controller\\Datalist';
        $datalist = new $class($this->resource);

        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $options['plugin_data']  = $this->plugin_data;

        if ((int)$parameters->grid_batch_categories === 1) {
            $this->plugin_data->grid_batch_categories = $datalist->getDatalist('Categories', $options);
        }
        if ((int)$parameters->grid_batch_tags === 1) {
            $this->plugin_data->grid_batch_tags = $datalist->getDatalist('Tags', $options);
        }
        if ((int)$parameters->grid_batch_permissions === 1) {
            $this->plugin_data->grid_batch_groups = $datalist->getDatalist('Groups', $options);
        }

        return $this;
    }
}
