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
use Exception;
use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;
use CommonApi\Exception\RuntimeException;

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
        if (strtolower($this->runtime_data->route->page_type) == 'grid') {
        } else {
            return $this;
        }

        $this->runtime_data->plugin_data->form_select_list = array();

        $this->runtime_data->plugin_data->grid = new stdClass();

        $this->getCurrentMenuItem();

        $this->setToolbar();

        $this->setGridFilter();

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
    protected function getCurrentMenuItem()
    {
        // 11102 or articles ... hmmmm ... hard to be unique qith grid, edit, list, etc.
        $model = 'Menuitem' . ':///Molajo//Menuitem//Articles';
        $this->runtime_data->current_menuitem = new stdClass();
        $this->runtime_data->current_menuitem->id = (int) $this->runtime_data->page->current_menuitem_id;
        $this->runtime_data->current_menuitem->extension = $this->resource->get($model);

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

        $list = $this->runtime_data->current_menuitem->extension->parameters->grid_toolbar_buttons;

        if ($list == '#' || $list == '') {
            $list = 'create,read,edit,publish,feature,archive,checkin,restore,delete,trash';
        }

        $grid_toolbar_buttons = explode(',', $list);
        $catalog_id           = $this->runtime_data->route->catalog_id;

        $temp_query_results = array();

        if (count($grid_toolbar_buttons) > 0) {

            foreach ($grid_toolbar_buttons as $button) {

                $options = array();
                $options['resource_id'] = $catalog_id;
                $options['task'] = $button;

                $permissions          = $this->authorisation_controller->isUserAuthorised($options);

                if ($permissions === true) {

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
        }

        if ($this->runtime_data->resource->parameters->grid_search == 1) {
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
     * @throws  /CommonApi/Exception/RuntimeException
     */
    protected function setGridFilter()
    {
        $grid_list = array();

        for ($i = 1; $i < 11; $i ++) {

            $grid_list_number = 'grid_list' . $i;
            if (isset($this->runtime_data->current_menuitem->extension->parameters->$grid_list_number)) {
                $grid_list[] = $this->runtime_data->current_menuitem->extension->parameters->$grid_list_number;
            } else {
                $grid_list[] = null;
            }
        }

        $lists = array();
/**        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $list) {

                //@todo figure out selected value
                $selected = '';

                if ((string)$list == '') {
                } else {
                    if (isset($this->runtime_data->plugin_data->datalists->$list)) {
                        $results = $this->runtime_data->plugin_data->datalists->$list;
                    } else {
                        throw new RuntimeException
                        ('PagetypegridPlugin: Unknown $this->runtime_data->plugin_data->datalists: ' . $list);
                    }

                    $key = $results->id;

                    $lists[$key] = $this->getFilter($results->value, $key);
                }
            }
        }
        echo '<pre>';
        var_dump($lists);
        die;
 */
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
        $controller              = $this->resource->get('query:///Molajo//' . $namespace . '.xml', $options);

        $controller->setModelRegistry('process_events', 0);
        $controller->setModelRegistry('get_customfields', 0);
        $controller->setModelRegistry('query_object', 'list');

        $catalog_type_id = $controller->getModelRegistry('catalog_type_id');

        if ((string)$catalog_type_id == '*') {
            if (isset($this->runtime_data->current_menuitem->extension->parameters->criteria_catalog_type_id)) {
                $catalog_type_id = $this->runtime_data->current_menuitem->extension->parameters->criteria_catalog_type_id;
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
        $resource = $this->runtime_data->current_menuitem->extension->parameters->menuitem_model_name;

        $model = 'Molajo//Datasource//' . $resource . '//Configuration.xml';
        $grid = $this->resource->get('query:///' . $model);

        $grid->setModelRegistry('check_view_level_access', 1);
        $grid->setModelRegistry('process_events', 1);
        $grid->setModelRegistry('query_object', 'list');
        $grid->setModelRegistry('get_customfields', 1);
        $grid->setModelRegistry('use_special_joins', 1);

        $primary_prefix = $grid->getModelRegistry('primary_prefix');
        $key            = $grid->getModelRegistry('primary_key');

        /** Select */
        for ($i = 1; $i < 16; $i ++) {

            $grid_column_number = 'grid_column' . $i;
            if (isset($this->runtime_data->current_menuitem->extension->parameters->$grid_column_number)) {
                $grid_column_list[] = trim($this->runtime_data->current_menuitem->extension->parameters->$grid_column_number);
            } else {
                $grid_column_list[] = null;
            }
        }

        $this->runtime_data->plugin_data->grid_columns = $grid_column_list;

        /** Catalog Type ID */
        if (isset($this->runtime_data->current_menuitem->extension->parameters->criteria_catalog_type_id)) {
            $grid->model->query->where(
                $grid->model->database->qn($primary_prefix)
                . '.'
                . $grid->model->database->qn('catalog_type_id')
                . ' = '
                . (int)$this->runtime_data->current_menuitem->extension->parameters->criteria_catalog_type_id
            );
        }

        /** Status */
        $list = '0,1,2';
        $grid->model->query->where(
            $grid->model->database->qn($primary_prefix)
            . '.' . $grid->model->database->qn('status')
            . ' IN (' . $list . ')'
        );

        /** Redirect To ID */
        $grid->model->query->where(
            $grid->model->database->qn('catalog.redirect_to_id')
            . ' = ' . 0
        );

        /** Ordering */
        $ordering = $this->runtime_data->current_menuitem->extension->parameters->grid_ordering;
        if ($ordering == '' || $ordering === null) {
            $ordering = $this->runtime_data->resource->model_registry['primary_key'];
        }

        $direction = $this->runtime_data->current_menuitem->extension->parameters->grid_ordering_direction;
        if ($direction == 'ASC') {
        } else {
            $direction = 'DESC';
        }

        $grid->model->query->order(
            $grid->model->database->qn($primary_prefix)
            . '.' . $grid->model->database->qn($ordering)
            . ' '
            . $direction
        );

        /** Offset */
//todo
//        $offset = $this->runtime_data->current_menuitem->extension->parameters->model_offset;
        $offset = 0;
        if ($offset == '' || $offset === null || (int)$offset == 0) {
            $offset = 0;
        }

        /** Items per Page */
        //$items_per_page = (int)$this->runtime_data->current_menuitem->extension->parameters->menuitem_model_count;
        $items_per_page = 15;
        if ((int)$items_per_page === 0) {
            $items_per_page = 15;
        }
//        $grid->setModelRegistry('model_count', $items_per_page);

        try {
            $results = $grid->getData();
        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        $name_key   = $grid->getModelRegistry('name_key');
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
        $temp = $this->runtime_data->current_menuitem->extension->parameters->grid_batch_array;

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
}
