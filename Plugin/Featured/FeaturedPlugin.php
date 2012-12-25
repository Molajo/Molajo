<?php
/**
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Featured;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Molajo
 * @license     MIT
 * @since       1.0
 */
class FeaturedPlugin extends Plugin
{
    /**
     * Retrieves Featured Content
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeInclude()
    {
        $model_type = Services::Registry()->get(ROUTE_PARAMETERS_LITERAL, 'model_type');
        $model_name = Services::Registry()->get(ROUTE_PARAMETERS_LITERAL, 'model_name');

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($model_type, $model_name, 1);

        $controller->set('get_customfields', 1, 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 1, 'model_registry');

        $primary_prefix = $controller->get('primary_prefix', 'a', 'model_registry');
        $primary_key = $controller->get('primary_key', 'id', 'model_registry');

        $controller->model->query->where(
            $controller->model->db->qn($primary_prefix)
            . '.'
            . $controller->model->db->qn('featured')
            . ' = 1 ');

        $results = $controller->getData(QUERY_OBJECT_LIST);

        Services::Registry()->set(
            TEMPLATE_LITERAL,
            $this->get('template_view_path_node', '', 'parameters'),
            $results
        );

        return true;
    }
}
