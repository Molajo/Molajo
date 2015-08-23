<?php
/**
 * Configuration Page Type Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeconfiguration;

use CommonApi\Event\DisplayEventInterface;
use stdClass;

/**
 * Configuration Page Type Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class PagetypeconfigurationPlugin extends Lists implements DisplayEventInterface
{
    /**
     * Get Data for Configuration Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->getFormData();
        $this->setPermissionGroups();
        $this->setViews();
        $this->setFormSections();
        $this->setFormSectionParameters('specific');
        $this->setPluginDataForms();
        $this->setPluginDataFormBeginValues('PUT', strtolower($this->runtime_data->route->page_type));

        return $this;
    }

    /**
     * On After Initialise Row
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterInitialise()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->setExtensionDefaults(
            $this->runtime_data->resource->catalog_type_id,
            $this->runtime_data->resource->data->catalog_view_group_id,
            1,
            $this->runtime_data->resource->data->criteria_extension_instance_id,
            $this->runtime_data->resource->data->criteria_extension_instance_id,
            $this->runtime_data->resource->data->model_name,
            strtolower(    $this->runtime_data->resource->data->model_name),
            $this->runtime_data->resource->data->criteria_catalog_type_id,
            $this->runtime_data->user->id,
            $this->runtime_data->user->username,
            $this->runtime_data->user->full_name
        );

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
        if (strtolower($this->runtime_data->route->page_type) === 'configuration') {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Initialise Plugin - establish form data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getFormData()
    {
        $this->getExtensionData();

        $this->getGridData();

        $this->setFormData();

        $this->getNewData();

        $this->getFieldLists($this->temp_model_registry);

        return $this;
    }

    /**
     * Get New Data Object
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getNewData()
    {
        $this->setCreateQueryObject();

        $row                            = $this->query->initialiseRow();
        $this->form_temp_data           = $this->query->executeOnAfterReadEvent($row);
        $this->form_temp_model_registry = $this->query->getModelRegistry();

        $this->setParameterDefaults();

        return $this;
    }

    /**
     * Set Resource Model Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCreateQueryObject()
    {
        $this->setModelNamespace();

        $this->setQueryController($this->model_namespace, 'Create');

        $this->setQueryControllerDefaults(
            $process_events = 1,
            $query_object = 'item',
            $get_customfields = 1,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        return $this;
    }

    /**
     * Set Model Namespace
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelNamespace()
    {
        $model_name = ucfirst(strtolower(trim($this->runtime_data->route->model_name)));
        $model_type = ucfirst(strtolower(trim($this->runtime_data->route->model_type)));

        $this->model_namespace = 'Molajo//' . $model_type . '//' . $model_name . '//Content.xml';

        return $this;
    }

    /**
     * Remove parameters from data and place into separate object
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setParameterDefaults()
    {
        $this->form_temp_parameters = new stdClass();

        if (isset($this->form_temp_data->parameters)) {
            $this->form_temp_parameters = $this->form_temp_data->parameters;
            unset($this->form_temp_data->parameters);
        }

        return $this;
    }

    /**
     * Get Extension Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getExtensionData()
    {
        $this->temp_data = $this->getExtension(
            ucfirst(strtolower(trim($this->runtime_data->resource->parameters->model_type))),
            ucfirst(strtolower(trim($this->runtime_data->resource->parameters->model_name)))
        );

        $this->setParameters();

        return $this;
    }

    /**
     * Get Grid Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridData()
    {
        $this->getGridMenuitem();

        $this->temp_data       = $this->sortObject($this->temp_data);
        $this->temp_parameters = $this->sortObject($this->temp_parameters);

        ksort($this->temp_model_registry['parameters']);

        return $this;
    }

    /**
     * Get Form Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setFormData()
    {
        $this->form_temp_parameters        = $this->temp_parameters;
        $this->form_temp_data              = $this->temp_data;
        $this->form_temp_model_registry    = $this->temp_model_registry;
        $this->form_array                  = $this->temp_parameters->configuration_array;
        $this->form_temp_customfieldgroups = $this->form_temp_model_registry['customfieldgroups'];

        if (isset($this->form_temp_model_registry['fields'])) {
            $this->initialiseFormModelRegistryFields();
        }

        if (count($this->form_temp_customfieldgroups) === 0) {
            return $this;
        }

        foreach ($this->form_temp_customfieldgroups as $customfieldgroup) {
            $this->initialiseFormModelRegistryCustomfields($customfieldgroup);
        }

        return $this;
    }
}
