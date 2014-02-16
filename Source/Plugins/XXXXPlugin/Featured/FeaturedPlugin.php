<?php
/**
 * Featured Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Featured;

use Exception;
use CommonApi\Exception\RuntimeException;
use CommonApi\Event\DisplayInterface;
use Molajo\Plugins\DisplayEventPlugin;

/**
 * Featured Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class FeaturedPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Retrieves Featured Content
     *
     * @return  $this
     * @since   1.0
     * @throws  \CommonApi\Exception\RuntimeException
     */
    public function onBeforeRenderView()
    {
        if (strtolower($this->plugin_data->render->scheme) == 'template'
            && strtolower($this->plugin_data->render->token->name == 'features')
        ) {
        } else {
            return $this;
        }

        $model = 'query:///Molajo//' . $this->runtime_data->route->model_name . '//Configuration.xml';

        $controller = $this->resource->get(
            $model,
            array('Parameters', $this->runtime_data)
        );

        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 1);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('use_special_joins', 1);
        $controller->setModelRegistry('get_item_children', 0);

        $primary_prefix = $controller->getModelRegistry('primary_prefix', 'a');

        $controller->model->query->where(
            $controller->model->database->qn($primary_prefix)
            . '.'
            . $controller->model->database->qn('featured')
            . ' = 1 '
        );

        try {
            $this->runtime_data->featured = $controller->getData();

        } catch (Exception $e) {
            throw new RuntimeException ($e->getMessage());
        }

        return $this;
    }
}
