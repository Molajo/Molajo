<?php
/**
 * Page Type Configuration Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypeconfiguration;

use CommonApi\Exception\UnexpectedValueException;
use CommonApi\Event\DisplayInterface;
use Exception;
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
        $this->setGridFieldFilter();
        $this->setFormFields();

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
        $catalog_id                    = $this->runtime_data->route->catalog_id;

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

    /**
     * Prepares Configuration Data
     *
     * @return  $this
     * @since   1.0
     */
    public function setFormFields()
    {
        $resource             = $this->plugin_data->resource->data;
        $extension_parameters = $this->plugin_data->resource->parameters;
        unset($resource->parameters);
        $metadata = $resource->metadata;
        unset($resource->metadata);
        $merged_parameters = $extension_parameters;
        foreach ($this->plugin_data->resource->menuitem->parameters as $key => $value) {
            $merged_parameters->$key = $value;
        }
        foreach ($metadata as $key => $value) {
            $merged_parameters->$key = $value;
        }

        $model_registry    = $this->plugin_data->resource->model_registry;
        $customfieldgroups = $model_registry['customfieldgroups'];
        $section_array     = $extension_parameters->configuration_array;
        echo $section_array;
        $this->setFormSections($section_array);
        $this->setFormSectionFieldsets($merged_parameters);
        $this->setFormFieldsetFields($merged_parameters, $model_registry, false);

        $template_views = array();
        foreach ($this->form_section_fieldsets as $key => $item) {
            $template_views[] = $key;
        }

        foreach ($template_views as $template) {

            $temp = array();

            foreach ($this->form_section_fieldset_fields as $item) {

                if ($template == $item->template_view) {

                    $name        = $item->name;
                    $item->value = null;

                    if (isset($merged_parameters->$name)) {
                        $item->value = $merged_parameters->$name;

                    } elseif (substr(strtolower($name), 0, strlen('metadata')) == 'metadata') {
                        if (isset($metadata->$name)) {
                            $item->value = $metadata->$name;
                        }
                    }

                    if ($item->value === null) {
                        $item->value = $item->default;
                    }

                    if (isset($item->datalist)) {
                        $item->type = 'selectlist';
//todo: fix
                        if ($item->datalist == 'Fieldsmetadata') {
                            $item->datalist = 'Metadata';
                        }
                        $item->list_name = strtolower($item->datalist);

                        $this->getSelectlist($item->datalist);
                    }

                    if (isset($item->type)) {
                    } else {
                        $item->type = 'char';
                    }

                    $temp[$name] = $item;
                }
            }

            $template = strtolower($template);

            $this->plugin_data->$template = $temp;
        }

        return $this;
    }

    /**
     * Get Select List and save results in plugin data
     *
     * @param   $list
     *
     * @return  $this
     *
     */
    public function getSelectlist($list)
    {
        //@todo figure out selected value
        $selected = '';

        $list = strtolower($list);

        if (isset($this->plugin_data->datalists->$list)) {
            $value = $this->plugin_data->datalists->$list;
        } elseif (isset($this->plugin_data->$list)) {
            return $this;
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

        $this->plugin_data->$list = $value;

        return $this;
    }
}
