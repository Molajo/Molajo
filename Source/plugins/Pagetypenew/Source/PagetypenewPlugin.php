<?php
/**
 * Page Type New Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypenew;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;
use stdClass;

/**
 * Page Type New Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class PagetypenewPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Before Rendering Page
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
        $this->getOrderingData($this->runtime_data->resource->data->id);
        $this->setFormArray();
        $this->setPluginDataForms();
        $this->setPluginDataFormBeginValues('POST', strtolower($this->runtime_data->route->page_type));

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
            $this->runtime_data->resource->data->catalog_type_id,
            $this->runtime_data->resource->data->catalog_primary_category_id,
            $this->runtime_data->resource->data->catalog_view_group_id,
            $this->runtime_data->resource->data->extension_id,
            $this->runtime_data->resource->data->id,
            $this->runtime_data->resource->data->title,
            $this->runtime_data->resource->data->alias,
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
        if (strtolower($this->runtime_data->route->page_type) === 'new') {
            return true;
        }

        return false;
    }

    /**
     * Establish form data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getFormData()
    {
        $this->getNewData();

        $this->setFormData();

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
     * Get Form Data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setFormData()
    {
        $this->form_array           = $this->form_temp_parameters->edit_array;

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
}
