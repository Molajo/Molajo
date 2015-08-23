<?php
/**
 * Page Type Configuration Base
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypeconfiguration;

use Molajo\Plugins\DisplayEvent;

/**
 * Page Type Configuration Base
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class Base extends DisplayEvent
{
    /**
     * Temp Data for Extension
     *
     * @var    object
     * @since  1.0.0
     */
    protected $temp_data;

    /**
     * Temp Parameters for Extension
     *
     * @var    object
     * @since  1.0.0
     */
    protected $temp_parameters;

    /**
     * Temp Model Registry for Extension
     *
     * @var    array
     * @since  1.0.0
     */
    protected $temp_model_registry;

    /**
     * Model Name
     *
     * @var    string
     * @since  1.0
     */
    protected $model_name = null;

    /**
     * Path
     *
     * @var    string
     * @since  1.0
     */
    protected $path = null;

    /**
     * Set Parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setParameters()
    {
        $parameters = $this->temp_data->parameters;
        unset($this->temp_data->parameters);

        $this->temp_parameters = $parameters;

        return $this;
    }

    /**
     * Set Model Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setModelRegistry()
    {
        $this->temp_model_registry = $this->query->getModelRegistry();

        return $this;
    }
}
