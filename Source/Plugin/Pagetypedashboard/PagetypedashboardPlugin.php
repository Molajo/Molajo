<?php
/**
 * Page Type Dashboard Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypedashboard;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;
use stdClass;
/**
 * Page Type Dashboard Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypedashboardPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares data for the Dashboard
     *
     * Dependent upon lists developed in onAfterRoute
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRender()
    {
        if (strtolower($this->runtime_data->route->page_type) == 'dashboard') {
        } else {
            return $this;
        }

        $this->plugin_data->form_select_list = array();
        $this->plugin_data->dashboard    = new stdClass();

        $this->getCurrentMenuItem();

        //$this->setToolbar();
        //$this->setGridFilter();
       // $this->setGridFieldFilter();

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
        //todo: fix
        $model                                           = 'Menuitem' . ':///Molajo//Menuitem//Configuration';
        $this->runtime_data->current_menuitem            = new stdClass();
        $this->runtime_data->current_menuitem->id        = $this->plugin_data->page->current_menuitem_id;
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
        $url = $this->plugin_data->page->urls['page'];

        $list = $this->plugin_data->resource->parameters->dashboard_toolbar_buttons;

        if ($list == '#' || $list == '') {
            $list = 'save';
        }

        $dashboard_toolbar_buttons = explode(',', $list);
        $catalog_id           = $this->runtime_data->route->catalog_id;

        $temp_query_results = array();

        if (count($dashboard_toolbar_buttons) > 0) {

            foreach ($dashboard_toolbar_buttons as $button) {

                $options                = array();
                $options['resource_id'] = $catalog_id;
                $options['task']        = $button;

                $permissions = true; //$this->authorisation_controller->isUserAuthorised($options);

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

        $this->plugin_data->dashboard_toolbar = $temp_query_results;

        return $this;
    }

    /**
     * Filters: lists stored in registry, where clauses for primary dashboard query set
     *
     * @return  $this
     * @since   1.0
     * @throws  /CommonApi/Exception/RuntimeException
     */
    protected function setGridFilter()
    {
        $dashboard_list = array();
        $temp      = array();

        for ($i = 1; $i < 11; $i ++) {

            $dashboard_list_number = 'dashboard_list' . $i;
            if (isset($this->runtime_data->current_menuitem->extension->parameters->$dashboard_list_number)) {
                if (trim($this->runtime_data->current_menuitem->extension->parameters->$dashboard_list_number) == '') {
                } else {
                    if (in_array($dashboard_list_number, $temp)) {
                    } else {
                        $temp[]         = $dashboard_list_number;
                        $row            = new stdClass();
                        $row->list_name = $this->runtime_data->current_menuitem->extension->parameters->$dashboard_list_number;
                        $dashboard_list[]    = $row;
                    }
                }
            }
        }

        $lists = array();

        if (is_array($dashboard_list) && count($dashboard_list) > 0) {

            foreach ($dashboard_list as $row) {

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

                    $listname = strtolower($list);
                    $this->plugin_data->$listname = $value;
                }
            }
        }

        $this->plugin_data->dashboard_filters = $dashboard_list;

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

        $this->plugin_data->dashboard_fields = $temp_array;

        return $this;
    }
}
