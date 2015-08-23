<?php
/**
 * Customfields Model Registry
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Customfields;

use CommonApi\Event\ReadEventInterface;

/**
 * Customfields Model Registry
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class CustomfieldsModelRegistry extends CustomfieldsContent implements ReadEventInterface
{
    /**
     * Set Model Registry for Custom Field Groups
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setCustomfieldGroupModelRegistry()
    {
        $resource_parameters = $this->initializeModelRegistryParameters();

        foreach ($this->controller['model_registry']['customfieldgroups'] as $group) {

            if ($group === 'parameters') {
                $this->setModelRegistryInheritedParameters($resource_parameters);
            } else {
                $this->setModelRegistryParameters($group);
            }
        }

        return $this;
    }

    /**
     * Set Model Registry for Custom Field Groups
     *
     * @param   array $resource_parameters
     *
     * @return  array
     * @since   1.0.0
     */
    protected function initializeModelRegistryParameters()
    {
        $this->model_registry_merged = $this->controller['model_registry'];

        $hold_parameters = $this->model_registry_merged['parameters'];
        unset($this->model_registry_merged['parameters']);

        $resource_parameters = array();

        if (isset($this->runtime_data->application->model_registry['parameters'])) {
            $resource_parameters = $this->runtime_data->application->model_registry['parameters'];
        }

        $this->model_registry_merged['parameters']
            = $this->setModelRegistryGroupKey($resource_parameters);

        return $hold_parameters;
    }

    /**
     * Set Application Parameters as Inherited
     *
     * @param   array $resource_parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistryInheritedParameters($resource_parameters)
    {
        if (count($resource_parameters) === 0) {
        } else {
            $this->model_registry_merged['parameters']
                = $this->setModelRegistryGroupKey($resource_parameters);
        }

        if (isset($this->model_registry_merged['parameters']['application_default_theme_id'])) {
            $this->model_registry_merged['parameters']['theme_id']
                = $this->model_registry_merged['parameters']['application_default_theme_id'];
            $this->model_registry_merged['parameters']['theme_id']['name']
                = 'theme_id';
        }

        return $this;
    }

    /**
     * Overlay Application Parameters with Resource Parameters
     *
     * @param   string $group
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistryParameters($group)
    {
        if (count($this->model_registry_merged[$group]) === 0) {
            return $this;
        }

        $resource_parameters = $this->model_registry_merged[$group];

        $this->model_registry_merged[$group] = $this->setModelRegistryGroupKey($resource_parameters);

        return $this;
    }

    /**
     * Set Application Parameters as Inherited
     *
     * @param   array $resource_parameters
     *
     * @return  array
     * @since   1.0.0
     */
    protected function setModelRegistryGroupKey($resource_parameters)
    {
        $new_array = array();

        foreach ($resource_parameters as $field) {
            $name                     = $field['name'];
            $field['field_inherited'] = 1;
            $new_array[$name]         = $field;
        }

        return $new_array;
    }
}
