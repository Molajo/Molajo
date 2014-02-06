<?php
/**
 * Page Type Configuration Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypeconfiguration;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;
use stdClass;
/**
 * Page Type Configuration Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypeconfigurationPlugin extends DisplayEventPlugin implements DisplayInterface
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
        if (strtolower($this->runtime_data->route->page_type) == 'configuration') {
        } else {
            return $this;
        }

        $this->plugin_data->form_select_list = array();
        $this->plugin_data->configuration    = new stdClass();

        $this->getCurrentMenuItem();

        $this->setToolbar();
        //$this->setGridFilter();
        $this->setGridFieldFilter();

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
        $resource                                        = 'Articles';
        $model                                           = 'Menuitem' . ':///Molajo//Menuitem//' . $resource;
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

        $list = $this->plugin_data->resource->parameters->configuration_toolbar_buttons;

        if ($list == '#' || $list == '') {
            $list = 'save';
        }

        $configuration_toolbar_buttons = explode(',', $list);
        $catalog_id           = $this->runtime_data->route->catalog_id;

        $temp_query_results = array();

        if (count($configuration_toolbar_buttons) > 0) {

            foreach ($configuration_toolbar_buttons as $button) {

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

        $this->plugin_data->configuration_toolbar = $temp_query_results;

        return $this;
    }

    /**
     * Filters: lists stored in registry, where clauses for primary configuration query set
     *
     * @return  $this
     * @since   1.0
     * @throws  /CommonApi/Exception/RuntimeException
     */
    protected function setGridFilter()
    {
        $configuration_list = array();
        $temp      = array();

        for ($i = 1; $i < 11; $i ++) {

            $configuration_list_number = 'configuration_list' . $i;
            if (isset($this->runtime_data->current_menuitem->extension->parameters->$configuration_list_number)) {
                if (trim($this->runtime_data->current_menuitem->extension->parameters->$configuration_list_number) == '') {
                } else {
                    if (in_array($configuration_list_number, $temp)) {
                    } else {
                        $temp[]         = $configuration_list_number;
                        $row            = new stdClass();
                        $row->list_name = $this->runtime_data->current_menuitem->extension->parameters->$configuration_list_number;
                        $configuration_list[]    = $row;
                    }
                }
            }
        }

        $lists = array();

        if (is_array($configuration_list) && count($configuration_list) > 0) {

            foreach ($configuration_list as $row) {

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

        $this->plugin_data->configuration_filters = $configuration_list;

        $this->plugin_data->configuration_batch_categories = $this->getFilter('Categories');
        $this->plugin_data->configuration_batch_tags       = $this->getFilter('Tags');
        $this->plugin_data->configuration_batch_groups     = $this->getFilter('Groups');

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

        $this->plugin_data->configuration_fields = $temp_array;

        return $this;
    }
}
