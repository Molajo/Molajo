<?php
/**
 * Page Type Configuration Grid Menuitem Class
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Plugins\Pagetypeconfiguration;

/**
 * Page Type Configuration Grid Menuitem Class
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
abstract class GridMenuitem extends Base
{
    /**
     * Get Grid Menu Item
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridMenuitem()
    {
        $grid = $this->getMenuItem(
            ucfirst(strtolower($this->runtime_data->resource->parameters->model_type)),
            ucfirst(strtolower($this->runtime_data->resource->parameters->model_name)),
            'Grid'
        );

        $temp           = $this->query->getModelRegistry();
        $parameters     = $grid->parameters;
        $model_registry = $temp['parameters'];

        $this->setGridMenuitemParameters($parameters, $model_registry);

        return $this;
    }

    /**
     * Set Grid Menuitem Parameters
     *
     * @param   object $parameters
     * @param   array  $model_registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setGridMenuitemParameters($parameters, array $model_registry)
    {
        foreach ($model_registry as $key => $field) {

            if (substr($key, 0, 5) === 'grid_' && isset($parameters->$key)) {
                $this->temp_parameters->$key = $parameters->$key;
                $this->setGridMenuitemModelRegistry($key, $field);
            }
        }

        return $this;
    }

    /**
     * Set Grid Menuitem Model Registry
     *
     * @param   string $key
     * @param   array  $field
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setGridMenuitemModelRegistry($key, array $field = array())
    {
        $this->temp_model_registry['parameters'][$key] = $field;

        return $this;
    }
}
