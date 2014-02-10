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
        $this->setFieldFilter();
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
        $resource                                        = $this->plugin_data->resource->resource_model_name;
        $model                                           = 'Menuitem' . ':///Molajo//Menuitem//' . $resource;
        $this->runtime_data->current_menuitem            = new stdClass();
        $this->runtime_data->current_menuitem->id        = (int)$this->plugin_data->page->current_menuitem_id;
        $this->runtime_data->current_menuitem->extension = $this->resource->get($model);

        return $this;
    }

    /**
     * Fields used by resource
     *
     * @return  $this
     * @since   1.0
     * @throws  /CommonApi/Exception/RuntimeException
     */
    protected function setFieldFilter()
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
     * Create form fields for configuration settings needed for resource
     *  including item, list, grid, edit, and configuration parameters and values
     *
     * Note: Using row, parameters, and model_registry since event does not populate
     *
     * @return  $this
     * @since   1.0
     */
    protected function setFormFields()
    {
        $parameters        = $this->plugin_data->resource->menuitem->parameters;
        $model_registry    = $this->plugin_data->resource->menuitem->model_registry;
        $customfieldgroups = $model_registry['customfieldgroups'];

        $section_array = $parameters->configuration_array;

        $this->setFormSections($section_array);

        echo '<pre>';
        var_dump($this->form_sections);
die;
        $this->setFormSectionFieldsets($parameters);
        die;
        $this->setFormFieldsetFields($parameters, $model_registry, false);
        die;
        $template_views = array();
        foreach ($this->form_section_fieldsets as $key => $item) {
            $template_views[] = $key;
        }

        /** Set non-custom field views */
        foreach ($template_views as $template) {
            $temp = array();
            foreach ($this->form_section_fieldset_fields as $item) {
                if ($template == $item->template_view) {
                    $temp[$item->name] = $this->setFormFieldProperties($item, $parameters, $metadata);
                }
            }
            $template                     = strtolower($template);
            $this->plugin_data->$template = $temp;
        }

        /** Customfields */
        foreach ($customfieldgroups as $customfield) {
            $this->setCustomfieldGroup($model_registry, $customfield);
        }

        return $this;
    }

    /**
     * Process Customfield Group
     *
     * @param   object $model_registry
     * @param   object $customfield
     *
     * @return  $this
     * @since   1.0
     */
    protected function setCustomfieldGroup($model_registry, $customfield)
    {
        $i      = 1;
        $fields = array();
        $temp   = array();

        if (isset($model_registry[$customfield])) {
            $fields = $model_registry[$customfield];
        }

        if (count($fields) > 0 && is_array($fields)) {
            foreach ($fields as $field) {
                if ((int)$field['field_inherited'] === 0) {
                    $item              = $this->setCustomfieldItem($field, $i, $customfield);
                    $temp[$item->name] = $this->setFormFieldProperties($item);
                }
            }
        }

        if (count($temp) > 0 && is_array($temp)) {
        } else {
            $field             = array();
            $field['name']     = 'none';
            $field['type']     = 'char';
            $field['null']     = 0;
            $field['default']  = '';
            $item              = $this->setCustomfieldItem($field, 1, $customfield);
            $temp[$item->name] = $this->setFormFieldProperties($item);
        }

        $plugin_data_name = 'configuration_' . $customfield;

        return $this;
    }

    /**
     * Prepares Configuration Data
     *
     * @param   object $field
     * @param   int    $i
     * @param   object $customfield
     *
     * @return  object
     * @since   1.0
     */
    protected function setCustomfieldItem($field, $i, $customfield)
    {
        $item                 = new stdClass();
        $item->id             = $i;
        $item->name           = $field['name'];
        $item->template_label = 'Customfields' . ucfirst(strtolower($customfield));
        $item->template_view  = 'Configuration_customfields';
        $item->field_masks    = strtolower($customfield) . '_*';
        $item->type           = $field['type'];
        $item->null           = $field['null'];
        $item->default        = $field['default'];
        $item->value          = null;

        return $item;
    }

    /**
     * Prepares Configuration Data
     *
     * @param   object      $item
     * @param   null|object $parameters
     * @param   null|object $metadata
     *
     * @return  object
     * @since   1.0
     */
    protected function setFormFieldProperties($item, $parameters = null, $metadata = null)
    {
        $name        = $item->name;
        $item->value = null;

        if (isset($parameters->$name)) {
            $item->value = $parameters->$name;

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

            if ($item->list_name == 'fields') {
                $item->list_name = 'configuration_fields';
            } else {
                $this->getSelectlist($item->datalist);
            }
        }

        if (isset($item->type)) {
        } else {
            $item->type = 'char';
        }

        return $item;
    }

    /**
     * Prepares Configuration Data
     *
     * @param   object $item
     * @param   object $parameters
     * @param   object $metadata
     *
     * @return  object
     * @since   1.0
     */
    protected function setCustomFields($item, $parameters, $metadata)
    {
        $name        = $item->name;
        $item->value = null;

        if (isset($parameters->$name)) {
            $item->value = $parameters->$name;

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


            if ($item->list_name == 'fields') {
                $item->list_name = 'configuration_fields';
            } else {
                $this->getSelectlist($item->datalist);
            }
        }

        if (isset($item->type)) {
        } else {
            $item->type = 'char';
        }

        return $item;
    }

    /**
     * Get Select List and save results in plugin data
     *
     * @param   $list
     *
     * @return  $this
     *
     */
    protected function getSelectlist($list)
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
