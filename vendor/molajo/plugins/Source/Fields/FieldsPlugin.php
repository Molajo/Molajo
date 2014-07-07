<?php
/**
 * Fields Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Fields;

use CommonApi\Event\DisplayInterface;

/**
 * Fields Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FieldsPlugin extends JoinFields implements DisplayInterface
{
    /**
     * Before Render
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->processFieldsPlugin() === false) {
            return $this;
        }

        return $this->setFields();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processFieldsPlugin()
    {
        if ($this->processFieldsPluginDashboard() === false) {
            return false;
        }

        return true;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function processFieldsPluginDashboard()
    {
        $page_type = strtolower($this->runtime_data->route->page_type);

        if ($page_type === 'dashboard') {
            return false;
        }

        return true;
    }

    /**
     * Generates list of Fields for select lists and defining Custom Fields
     *
     * @return  $this
     * @since   1.0.0
     */
    public function setFields()
    {
        $model_registry = $this->getModelRegistry();

        $fields = $model_registry['fields'];

        $this->setStandardFields($fields);
        $this->setCustomFields($model_registry);
        $this->setCustomFieldsJoins($model_registry);

        $this->setPluginDataStandardFields();
        $this->setPluginDataExtendedFields();
        $this->setPluginDataAllFields();

        return $this;
    }

    /**
     * Get the Model Registry
     *
     * @return  array
     * @since   1.0.0
     */
    protected function getModelRegistry()
    {
        if (isset($this->runtime_data->resource->menuitem->model_registry)) {
            $model_registry = $this->runtime_data->resource->menuitem->model_registry;
        } else {
            $model_registry = $this->runtime_data->resource->model_registry;
        }

        return $model_registry;
    }

    /**
     * Set Standard Fields Array
     *
     * @return  $this
     * @since    1.0.0
     */
    protected function setPluginDataStandardFields()
    {
        $this->plugin_data->fieldsstandard = $this->field_array;

        return $this;
    }

    /**
     * Set Extended Fields Array
     *
     * @return  $this
     * @since    1.0.0
     */
    protected function setPluginDataExtendedFields()
    {
        ksort($this->extended_field_array);
        $this->plugin_data->extended_field_array = $this->extended_field_array;

        return $this;
    }

    /**
     * Set All Fields Array
     *
     * @return  $this
     * @since    1.0.0
     */
    protected function setPluginDataAllFields()
    {
        asort($this->all_fields_array);
        $this->plugin_data->fields = $this->all_fields_array;

        return $this;
    }
}
