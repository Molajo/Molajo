<?php
/**
 * Page Type Edit Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeedit;

use CommonApi\Event\DisplayEventInterface;
use Molajo\Plugins\DisplayEvent;

/**
 * Page Type Edit Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class PagetypeeditPlugin extends DisplayEvent implements DisplayEventInterface
{
    /**
     * Get Data for Edit Page Type
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->initialisePlugin();

        $this->processPlugin();

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
        if (strtolower($this->runtime_data->route->page_type) === 'edit') {
            return true;
        }

        return false;
    }

    /**
     * Initialise Plugin - establish form data
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function initialisePlugin()
    {
        $this->form_temp_parameters     = $this->runtime_data->resource->parameters;
        $this->form_temp_data           = $this->runtime_data->resource->data;
        $this->form_temp_model_registry = $this->runtime_data->resource->model_registry;
        $this->form_array               = $this->runtime_data->resource->parameters->edit_array;

        $this->setModelNamespace();

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
     * Process Plugin
     *
     * $param   integer  catalog_type_id
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function processPlugin()
    {
        $this->initialiseClass();
        $this->getOrderingData(
            $this->runtime_data->resource->catalog_type_id,
            $this->form_temp_data->ordering
        );
        $this->setFormArray();
        $this->setPluginDataForms();
        $this->setPluginDataFormBeginValues('PUT', strtolower($this->runtime_data->route->page_type));

        return $this;
    }

    /**
     * Initialise Class
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function initialiseClass()
    {
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
