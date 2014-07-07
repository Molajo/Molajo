<?php
/**
 * Page Type Grid Plugin Base
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypegrid;

use Molajo\Plugins\DisplayEventPlugin;
use stdClass;

/**
 * Page Type Grid Plugin Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Base extends DisplayEventPlugin
{
    /**
     * Actions
     *
     * @var    array
     * @since  1.0.0
     */
    protected $actions = array(
        'grid_action_archive'   => 'archive',
        'grid_action_checkin'   => 'checkin',
        'grid_action_feature'   => 'feature',
        'grid_action_publish'   => 'publish',
        'grid_action_delete'    => 'delete',
        'grid_action_restore'   => 'restore',
        'grid_action_sticky'    => 'sticky',
        'grid_action_trash'     => 'trash',
        'grid_action_unpublish' => 'unpublish'
    );

    /**
     * Toolbar
     *
     * @var    array
     * @since  1.0.0
     */
    protected $toolbar = array(
        'grid_toolbar_button_copy'        => 'copy',
        'grid_toolbar_button_filter'      => 'filter',
        'grid_toolbar_button_new'         => 'new',
        'grid_toolbar_button_permissions' => 'permissions',
        'grid_toolbar_button_tags'        => 'tags'
    );

    /**
     * Button List
     *
     * @var    array
     * @since  1.0.0
     */
    protected $button_list = array();

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

        $this->initialiseButtonlist();
        $this->setActionList();
        $this->setToolbar();
        $this->getGridColumns();

        return $this;
    }

    /**
     * Get Data for Grid Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function initialiseButtonlist()
    {
        foreach ($this->actions as $key => $value) {
            if ((int)$this->runtime_data->resource->menuitem->parameters->$key === 1) {
                $this->button_list[] = $value;
            }
        }

        return $this;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setActionList()
    {
        $temp_query_results = array();

        foreach ($this->button_list as $button) {
            $temp_query_results = $this->setActionListItem($button, $temp_query_results);
        }

        $this->plugin_data->grid_actions = $temp_query_results;

        return $this;
    }

    /**
     * Create Action List Item
     *
     * @param   string $button
     * @param   array  $temp_query_results
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setActionListItem($button, $temp_query_results)
    {
        $results = $this->authoriseAction(
            $button,
            $this->runtime_data->route->catalog_id,
            $this->plugin_data->page->urls['page']
        );

        if ($results === false) {
        } else {
            $temp_query_results[] = $results;
        }

        return $temp_query_results;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setToolbar()
    {
        $temp_query_results = array();

        foreach ($this->setToolbarList() as $item) {

            $results = $this->authoriseAction(
                $item,
                $this->runtime_data->route->catalog_id,
                $this->plugin_data->page->urls['page']
            );

            if ($results === false) {
            } else {
                $temp_query_results[] = $results;
            }
        }

        $this->plugin_data->grid_toolbar = $temp_query_results;

        return $this;
    }

    /**
     * Create Toolbar List
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setToolbarList()
    {
        $parameters = $this->runtime_data->resource->menuitem->parameters;

        $list = array();
        foreach ($this->toolbar as $key => $value) {
            if ((int)$parameters->$key === 1) {
                $list[] = $value;
            }
        }

        return $list;
    }

    /**
     * Authorise action
     *
     * @param   string $button
     * @param   int    $catalog_id
     * @param   string $url
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function authoriseAction($button, $catalog_id, $url)
    {
        $options                = array();
        $options['resource_id'] = $catalog_id;
        $options['task']        = $button;

        $button_array = array('filter', 'permissions', 'tags', 'new', 'category');

        if (in_array($button, $button_array)) {
            $permissions = true;
        } else {
            $permissions = $this->authorisation_controller->isUserAuthorised($options);
        }

        if ($permissions === false) {
            return false;
        }

        return $this->authoriseActionRow($button, $url);
    }

    /**
     * Authorise action
     *
     * @param   string $button
     * @param   string $url
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function authoriseActionRow($button, $url)
    {
        $temp_row = new stdClass();

        $temp_row->name   = $this->language_controller->translateString(
            strtoupper('TASK_' . strtoupper($button) . '_BUTTON')
        );

        $temp_row->action = $button;

        $temp_row->link = $this->setTaskURL(
            $this->runtime_data->application->parameters->url_sef,
            $url,
            $temp_row->action
        );

        return $temp_row;
    }

    /**
     * Get Grid Columns
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridColumns()
    {
        $parameters = $this->runtime_data->resource->menuitem->parameters;
        $grid_column_list = array();

        for ($i = 1; $i < 16; $i ++) {
            $grid_column_list = $this->getGridColumnsItem($grid_column_list, $i, $parameters);
        }

        $this->plugin_data->grid_columns = $grid_column_list;

        return $this;
    }

    /**
     * Get Grid Column Item
     *
     * @param   array    $grid_column_list
     * @param   integer  $i
     * @param   array    $parameters
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
