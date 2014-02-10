<?php
/**
 * Page Type Grid Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypegrid;

use Exception;
use CommonApi\Event\DisplayInterface;
use CommonApi\Exception\RuntimeException;
use Molajo\Plugin\DisplayEventPlugin;
use stdClass;

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
    public function onBeforeRender()
    {
        if (strtolower($this->runtime_data->route->page_type) == 'grid') {
        } else {
            return $this;
        }

        $this->plugin_data->form_select_list = array();
        $this->plugin_data->grid             = new stdClass();

        $this->getCurrentMenuItem();
        $this->setActionList();
        $this->setToolbar();
        $this->getGridColumns();
        $this->getGridData();
        $this->setGridFilter();
        $this->setGridFieldFilter();
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
        $resource                                        = $this->plugin_data->resource->resource_model_name;
        $model                                           = 'Menuitem' . ':///Molajo//Menuitem//' . $resource;
        $this->runtime_data->current_menuitem            = new stdClass();
        $this->runtime_data->current_menuitem->id        = (int)$this->plugin_data->page->current_menuitem_id;
        $this->runtime_data->current_menuitem->extension = $this->resource->get($model);

        return $this;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return  $this
     * @since   1.0
     */
    protected function setActionList()
    {
        $url = $this->plugin_data->page->urls['page'];

        $list = array();

        $parameters = $this->plugin_data->resource->menuitem->parameters;

        if ((int)$parameters->grid_action_archive == 1) {
            $list[] = 'archive';
        }
        if ((int)$parameters->grid_action_checkin == 1) {
            $list[] = 'checkin';
        }
        if ((int)$parameters->grid_action_feature == 1) {
            $list[] = 'feature';
        }
        if ((int)$parameters->grid_action_publish == 1) {
            $list[] = 'publish';
        }
        if ((int)$parameters->grid_action_delete == 1) {
            $list[] = 'delete';
        }
        if ((int)$parameters->grid_action_restore == 1) {
            $list[] = 'restore';
        }
        if ((int)$parameters->grid_action_sticky == 1) {
            $list[] = 'sticky';
        }
        if ((int)$parameters->grid_action_trash == 1) {
            $list[] = 'trash';
        }
        if ((int)$parameters->grid_action_unpublish == 1) {
            $list[] = 'unpublish';
        }

        $catalog_id = $this->runtime_data->route->catalog_id;

        $temp_query_results = array();

        if (count($list) > 0) {

            foreach ($list as $button) {

                $results = $this->authoriseAction($button, $catalog_id, $url);

                if ($results === false) {
                } else {
                    $temp_query_results[] = $results;
                }
            }
        }

        $this->plugin_data->grid_actions = $temp_query_results;

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
        $url        = $this->plugin_data->page->urls['page'];
        $parameters = $this->plugin_data->resource->menuitem->parameters;
        $list       = array();

        if ((int)$parameters->grid_toolbar_button_copy == 1) {
            $list[] = 'copy';
        }
        if ((int)$parameters->grid_toolbar_button_filter == 1) {
            $list[] = 'filter';
        }
        if ((int)$parameters->grid_toolbar_button_new == 1) {
            $list[] = 'new';
        }
        if ((int)$parameters->grid_toolbar_button_permissions == 1) {
            $list[] = 'permissions';
        }
        if ((int)$parameters->grid_toolbar_button_tags == 1) {
            $list[] = 'tags';
        }

        $catalog_id = $this->runtime_data->route->catalog_id;

        $temp_query_results = array();

        if (count($list) > 0) {

            foreach ($list as $button) {

                $results = $this->authoriseAction($button, $catalog_id, $url);

                if ($results === false) {
                } else {
                    $temp_query_results[] = $results;
                }
            }
        }

        $this->plugin_data->grid_toolbar = $temp_query_results;

        return $this;
    }

    /**
     * Authorise action
     *
     * @param   string $button
     * @param   int    $catalog_id
     * @param   string $url
     *
     * @return  $this
     * @since   1.0
     */
    protected function authoriseAction($button, $catalog_id, $url)
    {
        $options                = array();
        $options['resource_id'] = $catalog_id;
        $options['task']        = $button;

        if ($button == 'filter'
            || $button == 'permissions'
            || $button == 'tags'
            || $button == 'new'
            || $button == 'category'
        ) {
            $permissions = true;
        } else {
            $permissions = $this->authorisation_controller->isUserAuthorised($options);
        }

        if ($permissions === false) {
            return false;
        }

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

        return $temp_row;
    }

    /**
     * Get Grid Columns
     *
     * @return  $this
     * @since   1.0
     */
    protected function getGridColumns()
    {
        $parameters = $this->plugin_data->resource->menuitem->parameters;

        for ($i = 1; $i < 16; $i ++) {

            $grid_column_number = 'grid_column' . $i;
            if (isset($parameters->$grid_column_number)) {

                $field = trim($parameters->$grid_column_number);
                if (trim($field) == '') {
                } else {
                    $grid_column_list[] = $field;
                }
            }
        }

        $this->plugin_data->grid_columns = $grid_column_list;

        return $this;
    }

    /**
     * Grid Query
     *
     * @return  $this
     * @since   1.0
     */
    protected function getGridData()
    {
        $parameters      = $this->plugin_data->resource->menuitem->parameters;
        $resource        = $this->plugin_data->resource->resource_model_name;
        $catalog_type_id = $parameters->criteria_catalog_type_id;
        $model           = 'Molajo//Datasource//' . $resource . '//Configuration.xml';
        $grid            = $this->resource->get('query:///' . $model);

        $grid->setModelRegistry('check_view_level_access', 1);
        $grid->setModelRegistry('process_events', 1);
        $grid->setModelRegistry('query_object', 'list');
        $grid->setModelRegistry('get_customfields', 1);
        $grid->setModelRegistry('use_special_joins', 1);

        $primary_prefix = $grid->getModelRegistry('primary_prefix');
        $key            = $grid->getModelRegistry('primary_key');

        /** Catalog Type ID */
        $grid->model->query->where(
            $grid->model->database->qn($primary_prefix)
            . '.'
            . $grid->model->database->qn('catalog_type_id')
            . ' = '
            . (int)$catalog_type_id
        );

        /** Status */
//        $list = $parameters->grid_status;
        $list = '';
        if ($list == '' || trim($list) == '' || $list === null) {
        } else {
            $grid->model->query->where(
                $grid->model->database->qn($primary_prefix)
                . '.' . $grid->model->database->qn('status')
                . ' IN (' . $list . ')'
            );
        }

        /** Redirect To ID */
        $grid->model->query->where(
            $grid->model->database->qn('catalog.redirect_to_id')
            . ' = ' . 0
        );

        if ((int)$parameters->grid_pagination_use == 1) {

            $ordering = $parameters->grid_pagination_ordering_column;
            if ($ordering == '' || $ordering === null) {
                $ordering = $this->plugin_data->resource->model_registry['primary_key'];
            }

            $direction = $parameters->grid_pagination_ordering_direction;
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
            $offset = 0;
            if ($offset == '' || $offset === null || (int)$offset == 0) {
                $offset = 0;
            }
            $grid->setModelRegistry('model_offset', $offset);

            /** Items per Page */
            $items_per_page = (int)$parameters->grid_pagination_items_per_page;
            if ((int)$items_per_page === 0) {
                $items_per_page = 10;
            }

            $grid->setModelRegistry('model_count', $items_per_page);
            $grid->setModelRegistry('model_use_pagination', 1);
        } else {
            $grid->setModelRegistry('model_offset', 0);
            $grid->setModelRegistry('model_count', 999999);
            $grid->setModelRegistry('model_use_pagination', 0);
        }

        try {
            $results = $grid->getData();

        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        $name_key = $grid->getModelRegistry('name_key');

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

        $this->plugin_data->grid_data           = $grid_items;
        $this->plugin_data->grid_model_registry = $grid->model->model_registry;

        /** Grid Ordering Template */
        $temp                             = new stdClass();
        $temp->ordering                   = $ordering;
        $temp->direction                  = $direction;
        $temp->items_per_page             = $items_per_page;
        $temp->offset                     = $offset;
        $this->plugin_data->grid_ordering = $temp;

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
        $parameters = $this->plugin_data->resource->menuitem->parameters;

        $grid_list = array();
        $temp      = array();

        for ($i = 1; $i < 11; $i ++) {

            $grid_list_number = 'grid_filter_list' . $i;

            if (isset($parameters->$grid_list_number)) {
                if (trim($parameters->$grid_list_number) == '') {
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

        $lists = array();

        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $row) {

                //@todo figure out selected value
                $selected = '';

                $list = $row->list_name;

                if ((string)$list == '') {
                } else {

                    if (isset($this->plugin_data->datalists->$list)) {
                        $value = $this->plugin_data->datalists->$list;
                    } else {
                        $value = $this->getFilter($list);
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
     * @since   1.0
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
                $temp->list_name    = $this->language_controller->translate('Fields');
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
     * @since   1.0
     */
    protected function setFirstLastEvenOdd()
    {
        $rows = $this->plugin_data->grid_data;

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

        $this->plugin_data->grid_data = $rows;

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
        $parameters = $this->plugin_data->resource->menuitem->parameters;

        if ((int)$parameters->grid_batch_categories == 1) {
            $this->plugin_data->grid_batch_categories = $this->getFilter('Categories');
        }
        if ((int)$parameters->grid_batch_tags == 1) {
            $this->plugin_data->grid_batch_tags = $this->getFilter('Tags');
        }
        if ((int)$parameters->grid_batch_permissions == 1) {
            $this->plugin_data->grid_batch_groups = $this->getFilter('Groups');
        }

        return $this;
    }
}
