<?php
/**
 * Page Type Grid Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypegrid;

use stdClass;
use CommonApi\Event\DisplayInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Page Type Grid Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypegridPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares data for the Administrator Grid
     *
     * Dependent upon lists developed in onAfterRoute
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->runtime_data->route->page_type)
            == strtolower($this->runtime_data->reference_data->page_type_grid)
        ) {
        } else {
            return $this;
        }

        // Initiate
        $this->runtime_data->plugin_data->form_select_list = array();

        $this->runtime_data->plugin_data->grid = new stdClass();

        $this->setToolbar();

        $this->setFilter();

        $this->getGridData();

        $this->setFirstLastEvenOdd();

        $this->setBatch();

        return $this;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return  $this
     * @since   1.0
     */
    protected function setToolbar()
    {
        $url = $this->runtime_data->page->urls['page'];

        $list = $this->runtime_data->resource->data->parameters->grid_toolbar_buttons;

        if ($list == '#' || $list == '') {
            $list = 'create,read,edit,publish,feature,archive,checkin,restore,delete,trash';
        }

        $grid_toolbar_buttons = explode(',', $list);
        $catalog_id           = $this->runtime_data->route->catalog_id;
        $permissions          = $this->isUserAuthorisedTasks(
            $grid_toolbar_buttons,
            $catalog_id
        );


        $temp_query_results = array();

        foreach ($grid_toolbar_buttons as $button) {

            if ($permissions[$button] === true) {
                $temp_row = new stdClass();

                $temp_row->name   = $this->language_controller->translate(
                    strtoupper('TASK_' . strtoupper($button) . '_BUTTON')
                );
                $temp_row->action = $button;

                if ($this->runtime_data->application->parameters->url_sef == 1) {
                    $temp_row->link = $url . '/task/' . $temp_row->action;
                } else {
                    $temp_row->link = $url . '&task=' . $temp_row->action;
                }

                $temp_query_results[] = $temp_row;
            }
        }

        if ($this->runtime_data->resource->data->parameters->grid_search == 1) {
            $temp_row = new stdClass();

            $temp_row->name   = $this->language_controller->translate(strtoupper('TASK_' . 'SEARCH' . '_BUTTON'));
            $temp_row->action = 'search';

            if ($this->runtime_data->application->parameters->url_sef == 1) {
                $temp_row->link = $url . '/task/' . $temp_row->action;
            } else {
                $temp_row->link = $url . '&task=' . $temp_row->action;
            }

            $temp_query_results[] = $temp_row;
        }

        $this->runtime_data->plugin_data->grid_toolbar = $temp_query_results;

        return $this;
    }

    /**
     * Filters: lists stored in registry, where clauses for primary grid query set
     *
     * @return  $this
     * @since   1.0
     */
    protected function setFilter()
    {
        $grid_list = array();

        for ($i = 1; $i < 11; $i ++) {

            $grid_list_number = 'grid_list' . $i;
            if (isset($this->runtime_data->resource->data->parameters->$grid_list_number)) {
                $grid_list[] = $this->runtime_data->resource->data->parameters->$grid_list_number;
            } else {
                $grid_list[] = null;
            }
        }

        $lists = array();
        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $list) {

                //@todo figure out selected value
                $selected = '';

                if ((string)$list == '') {
                } else {
                    if (isset($this->runtime_data->plugin_data->datalists->datalist[$list])) {
                        $results = $this->runtime_data->plugin_data->datalists->datalist[$list];
                    } else {
                        throw new RuntimeException
                        ('PagetypegridPlugin: Unknown $this->runtime_data->plugin_data->datalists->datalist: ' . $list);
                    }

                    $key = $results->id;

                    $lists[$key] = $this->getFilter($results->value, $key);
                }
            }
        }

        /** Fields */
        $temp_array = array();

        $fields = $this->runtime_data->resource->model_registry['fields'];
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {

                $temp = new stdClass();

                if ($field['type'] == 'customfield') {
                } else {
                    $temp->id       = $field['name'];
                    $temp->name     = $field['name'];
                    $temp->multiple = '';
                    $temp->size     = 1;
                    $temp_array[]   = $temp;
                }
            }
        }

        $fields = $this->runtime_data->resource->model_registry['parameters'];
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {

                $temp = new stdClass();

                if ($field['type'] == 'customfield') {
                } else {
                    $temp->id       = $field['name'];
                    $temp->name     = $field['name'];
                    $temp->multiple = '';
                    $temp->size     = 1;
                    $temp_array[]   = $temp;
                }
            }
        }

        $fields = $this->runtime_data->resource->model_registry['metadata'];
        if (is_array($fields) && count($fields) > 0) {
            foreach ($fields as $field) {

                $temp = new stdClass();

                if ($field['type'] == 'customfield') {
                } else {
                    $temp->id       = $field['name'];
                    $temp->name     = $field['name'];
                    $temp->multiple = '';
                    $temp->size     = 1;
                    $temp_array[]   = $temp;
                }
            }
        }
        $lists['Fields'] = $temp_array;

        $this->runtime_data->plugin_data->grid_filters = $lists;

        return $this;
    }

    /**
     * Get Filter
     *
     * @return  int
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    protected function getFilter($namespace, $list)
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;
        $controller              = $this->resources->get('query:///' . $namespace . '.xml', $options);

        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'list');

        $catalog_type_id = $controller->getModelRegistry('catalog_type_id');

        if ((string)$catalog_type_id == '*') {
            if (isset($this->runtime_data->resource->data->parameters->criteria_catalog_type_id)) {
                $catalog_type_id = $this->runtime_data->resource->data->parameters->criteria_catalog_type_id;
            }
        }
        if ((int)$catalog_type_id === 0) {
        } else {
            $controller->model->query->where(
                $controller->model->database->qn($controller->get('primary_prefix', 'a'))
                . '.' . $controller->model->database->qn('catalog_type_id')
                . ' = '
                . (int)$catalog_type_id
            );
        }

        try {
            $results = $controller->getData();
        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        return $results;
    }

    /**
     * Grid Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function getGridData()
    {
        $resource = $this->runtime_data->resource->data->parameters->model_name;
        if ($resource == '') {
            $resource = 'Articles';
        }

        $model = 'Molajo//Datasource//' . $resource . '//Configuration.xml';
        $query = $this->resources->get('query:///' . $model);

        $query->setModelRegistry('check_view_level_access', 0);
        $query->setModelRegistry('process_events', 1);
        $query->setModelRegistry('get_item_children', 0);
        $query->setModelRegistry('use_special_joins', 1);

        $primary_prefix = $query->getModelRegistry('primary_prefix');
        $key            = $query->getModelRegistry('primary_key');

        /** Select */
        for ($i = 1; $i < 16; $i ++) {

            $grid_column_number = 'grid_column' . $i;
            if (isset($this->runtime_data->resource->data->parameters->$grid_column_number)) {
                $grid_column_list[] = trim($this->runtime_data->resource->data->parameters->$grid_column_number);
            } else {
                $grid_column_list[] = null;
            }
        }

        $this->runtime_data->plugin_data->grid_columns = $grid_column_list;

        /** Catalog Type ID */
        if (isset($this->runtime_data->resource->data->parameters->criteria_catalog_type_id)) {
            $query->model->query->where(
                $query->model->database->qn($primary_prefix)
                . '.'
                . $query->model->database->qn('catalog_type_id')
                . ' = '
                . (int)$this->runtime_data->resource->data->parameters->criteria_catalog_type_id
            );
        }

        /** Status */
        $list = '0,1,2';
        $query->model->query->where(
            $query->model->database->qn($primary_prefix)
            . '.' . $query->model->database->qn('status')
            . ' IN (' . $list . ')'
        );

        /** Redirect To ID */
        $query->model->query->where(
            $query->model->database->qn('catalog.redirect_to_id')
            . ' = ' . 0
        );

        /** Ordering */
        $ordering = $this->runtime_data->resource->data->parameters->grid_ordering;
        if ($ordering == '' || $ordering === null) {
            $ordering = $this->runtime_data->resource->model_registry['primary_key'];
        }

        $direction = $this->runtime_data->resource->data->parameters->grid_ordering_direction;
        if ($direction == 'ASC') {
        } else {
            $direction = 'DESC';
        }

        $query->model->query->order(
            $query->model->database->qn($primary_prefix)
            . '.' . $query->model->database->qn($ordering)
            . ' '
            . $direction
        );

        /** Offset */
        $offset = $this->runtime_data->resource->data->parameters->model_offset;
        if ($offset == '' || $offset === null || (int)$offset == 0) {
            $offset = 0;
        }

        /** Items per Page */
        $items_per_page = (int)$this->runtime_data->resource->data->parameters->model_count;
        if ((int)$items_per_page === 0) {
            $items_per_page = 15;
        }
        $query->setModelRegistry('model_count', $items_per_page);

        try {
            $results = $query->getData();
        } catch (Exception $e) {
            throw new ControllerException ($e->getMessage());
        }

        $name_key   = $query->getModelRegistry('name_key');
        $grid_items = array();
        if (count($results) > 0) {
            foreach ($results as $item) {
                $temp_row = new stdClass();
                $name     = $item->$name_key;
                $temp_row = $item;

                if (isset($item->lvl)) {
                } else {
                    $grid_items[] = $item;
                    break;
                }

                $lvl = (int)$item->lvl - 1;

                if ($lvl > 0) {
                    for ($i = 0; $i < $lvl; $i ++) {
                        $name = ' ..' . $name;
                    }
                }
                $temp_row->$name_key = $name;

                $grid_items[] = $temp_row;
            }
        }

        $this->runtime_data->plugin_data->grid_data = $grid_items;

        /** Grid Ordering Template */
        $temp                                           = new stdClass();
        $temp->ordering                                 = $ordering;
        $temp->direction                                = $direction;
        $temp->items_per_page                           = $items_per_page;
        $temp->offset                                   = $offset;
        $this->runtime_data->plugin_data->grid_ordering = $temp;

        return $this;
    }

    /**
     * First, Even/Odd and Last Rows
     *
     * @return  $this
     * @since   1.0
     */
    protected function setFirstLastEvenOdd()
    {
        $rows = $this->runtime_data->plugin_data->grid_data;

        $total_rows      = count($rows);
        $even_or_odd_row = 'odd ';
        $count           = 0;
        if ($total_rows == 0) {
        } else {
            foreach ($rows as $row) {
                $count ++;

                if ($count == 1) {
                    $row->first_row = 'first ';
                } else {
                    $row->first_row = '';
                }

                $row->even_or_odd_row = $even_or_odd_row;
                if ($even_or_odd_row == 'odd ') {
                    $even_or_odd_row = 'even ';
                } else {
                    $even_or_odd_row = 'odd ';
                }

                $row->total_rows = $total_rows;

                if ($total_rows == $count) {
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

        $this->runtime_data->plugin_data->grid_data = $rows;

        return $this;
    }

    /**
     * Creates and stores lists for Grid Batch area
     *
     * @return  $this
     * @since   1.0
     */
    protected function setBatch()
    {
        $temp = $this->runtime_data->resource->data->parameters->grid_batch_array;

        if (trim($temp) == '') {
            $this->runtime_data->page->menu['SectionSubmenu'] = array();

            return $this;
        }

        $grid_batch_array = explode(',', $temp);
        if (count($grid_batch_array) == 0) {
            $this->runtime_data->page->menu['SectionSubmenu'] = array();
            return $this;
        }

        $grid_batch = array();

        for ($i = 0; $i < count($grid_batch_array); $i ++) {

            $enable = 'grid_batch_' . strtolower($grid_batch_array[$i]);


            if ((int)$enable == 0) {
            } else {

                $grid_batch[] = strtolower($grid_batch_array[$i]);

                $temp_row           = new stdClass();
                $temp_row->selected = '';
                $temp_row->enable   = 1;

                $name = 'grid_batch_' . strtolower($grid_batch_array[$i]);

                $this->runtime_data->plugin_data->$name = array($temp);
            }
        }


        $pageArray = array();
        $i         = 0;

        foreach ($grid_batch as $item) {
            if ($item == '') {
            } else {
                $temp_row = new stdClass();

                $temp_row->id = strtolower($item);
                if ($i == 0) {
                    $temp_row->current = 1;
                } else {
                    $temp_row->current = 0;
                }
                $temp_row->title = ucfirst(strtolower($item));
                $temp_row->url   = $this->runtime_data->page->urls['page'] . '#lk' . strtolower($item);

                $pageArray[] = $temp_row;

                $i ++;
            }
        }

        $this->runtime_data->page->menu['SectionSubmenu'] = $pageArray;

        return $this;
    }

    /**
     * Is User Authorised for this Site
     *
     * @return  bool
     * @since   1.0
     */
    protected function isUserAuthorisedTasks($grid_toolbar_buttons, $catalog_id)
    {
        if (count($grid_toolbar_buttons) === 0) {
            return array();
        }

        $permission_array = array();

        foreach ($grid_toolbar_buttons as $task) {

            $action = $this->getTaskAction($task);

            if ($action === false) {
                $permission_array[$task] = false;
            } else {
                $options = array();
                $options['task'] = $task;
                $options['resource_id'] = $catalog_id;
                $permission_array[$task]
                    = $this->authorisation_controlleruthorisation->isUserAuthorised($options);
            }
        }

        return $permission_array;
    }

}
