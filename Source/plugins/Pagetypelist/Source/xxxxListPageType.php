<?php
/**
 * List Resource Query
 *
 * @package    Molajo
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 */
namespace Molajo\Controller\Resource;

use CommonApi\Application\ResourceInterface;

/**
 * List Resource Query
 *
 * @author     Amy Stephen
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @since      1.0.0
 */
class ListPageType extends Extension implements ResourceInterface
{
    /**
     * Get Resource Data for Route
     *
     * @return  object
     * @since   1.0.0
     */
    protected function getResource()
    {
        $controller                     = $this->setResourceExtensionQuery();
        $this->resource->data           = $this->runQuery($controller);
        $this->resource->model_registry = $controller->getModelRegistry();
        $this->resource->model_name     = $this->model_name;

        parent::getResource();

        return $this->resource;
    }

    /**
     * Set Resource Extension Query
     *
     * @return  object
     * @since   1.0.0
     */
    protected function setResourceExtensionQuery()
    {
        $this->model_name = 'Articles';
        $parameters       = $this->runtime_data->resource->parameters;
        $model_name       = ucfirst(strtolower(trim($parameters->model_name)));
        $model_type       = ucfirst(strtolower(trim($parameters->model_type)));
        $model            = 'Molajo//' . $model_type . '//' . $model_name . '//Content.xml';


        $controller = $this->resource->get('query://' . $model, array('runtime_data' => $this->runtime_data));

        $catalog_type_id = (int)$controller->getModelRegistry('criteria_catalog_type_id', 0);
        $prefix          = $controller->getModelRegistry('primary_prefix', 'a');

        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 1);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('use_special_joins', 1);
        $controller->where('column', $prefix . '.catalog_type_id', '=', 'integer', $catalog_type_id);

        return $controller;
    }
}
