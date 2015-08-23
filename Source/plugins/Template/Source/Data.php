<?php
/**
 * Data
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Template;

use Molajo\Plugins\DisplayEvent;
use stdClass;

/**
 * Set Data for Template
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
abstract class Data extends DisplayEvent
{
    /**
     * Get Data for Rendering
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getTemplateRenderData()
    {
//        echo '<br><br>';
//        echo 'Token Name: ' . $this->controller['parameters']->token->name . '<br>';
//        echo 'Model Type: ' . $this->controller['parameters']->token->model_type . '<br>';
//        echo 'Model Name: ' . $this->controller['parameters']->token->model_name . '<br>';
//
        $this->token = $this->controller['parameters']->token;

        if ($this->controller['parameters']->token->model_type === 'plugin_data') {
            $this->setPluginData();

        } elseif ($this->controller['parameters']->token->model_type === 'runtime_data'
            && $this->controller['parameters']->token->model_name === 'resource'
        ) {
            $this->getPrimaryData();
        }

        $this->setDataResults();

        return $this;
    }

    /**
     * Get Data from Primary Data Collection
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getPrimaryData()
    {
        $this->controller['query_results']  = $this->runtime_data->resource->data;
        $this->controller['model_registry'] = $this->runtime_data->resource->model_registry;
        $this->controller['parameters']     = $this->runtime_data->resource->parameters;

        if (isset($this->plugin_data->render->extension->parameters)) {

            $hold_parameters = $this->plugin_data->render->extension->parameters;

            if (is_array($hold_parameters) && count($hold_parameters) > 0) {
                $this->getPrimaryDataExtensionParameters($hold_parameters);
            }
        }

        return $this;
    }

    /**
     * Get Parameter Data from Primary Data Collection
     *
     * @param   array $hold_parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getPrimaryDataExtensionParameters($hold_parameters)
    {
        foreach ($hold_parameters as $key => $value) {
            if (isset($this->controller['parameters']->$key)) {
                if ($this->controller['parameters']->$key === null) {
                    $this->controller['parameters']->$key = $value;
                }
            } else {
                $this->controller['parameters']->$key = $value;
            }
        }
    }

    /**
     * Get Data from Plugin Data Collection
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setPluginData()
    {
        $name = $this->controller['parameters']->token->model_name;

        if (isset($this->plugin_data->$name)) {
            $this->getPluginDataQueryResults();
        }

        $this->setDataParameters();

        return $this;
    }

    /**
     * Get Data from Plugin Data Collection
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getPluginDataQueryResults()
    {
        $name = $this->controller['parameters']->token->model_name;

        if (isset($this->plugin_data->$name->data)) {
            $this->getPluginDataTwoParts($this->plugin_data->$name);
        } else {
            $this->controller['query_results'] = $this->plugin_data->$name;
        }

        $this->getPluginDataQueryResultsField();

        return $this;
    }

    /**
     * Get Data from Runtime Data Collection where there is a Data and Model Registry
     *
     * @param   object $data_object
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getPluginDataTwoParts($data_object)
    {
        if (is_array($data_object->data)) {
            $this->controller['query_results'] = $data_object->data;
        } else {
            $this->controller['query_results'] = array($data_object->data);
        }

        if (isset($data_object->model_registry)) {
            $this->controller['model_registry'] = $data_object->model_registry;
        }

        return $this;
    }

    /**
     * Reduce Query object to specific field requested
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getPluginDataQueryResultsField()
    {
        if (is_array($this->controller['query_results'])) {
            if (isset($this->controller['query_results'][$this->controller['parameters']->token->field_name])) {
                $x                                   = $this->controller['query_results'][$this->controller['parameters']->token->field_name];
                $this->controller['query_results']   = array();
                $this->controller['query_results'][] = $x;
            }
        }

        return $this;
    }

    /**
     * Set Parameters from Query Results or Extension Parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setDataParameters()
    {
        if (isset($this->controller['query_results']->parameters)) {
            $this->controller['parameters'] = $this->controller['query_results']->parameters;
            unset($this->controller['query_results']->parameters);
            $this->controller['query_results'] = $this->controller['query_results']->data;

        } else {
            $this->controller['parameters'] = $this->plugin_data->render->extension->parameters;
        }

        return $this;
    }

    /**
     * Set data for return
     *
     * @return  stdClass
     * @since   1.0.0
     */
    protected function setDataResults()
    {
        $this->setQueryResultsArray();
        $this->setParametersFromToken();

        return $this;
    }

    /**
     * Ensure Query Results are an array
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setQueryResultsArray()
    {
        if (is_array($this->controller['query_results'])) {
        } else {
            $this->controller['query_results'] = array($this->controller['query_results']);
        }

        return $this;
    }

    /**
     * Set Parameters from Token
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setParametersFromToken()
    {
        if (count($this->token->attributes) > 0) {
            foreach ($this->token->attributes as $key => $value) {
                $this->controller['parameters']->$key = $value;
            }
        }

        $this->controller['parameters']->token = $this->token;

        return $this;
    }
}
