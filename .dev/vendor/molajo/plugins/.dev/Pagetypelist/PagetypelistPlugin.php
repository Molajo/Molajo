<?php
/**
 * Page Type List Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypelist;

use Molajo\Plugins\DisplayEventPlugin;
use CommonApi\Event\DisplayInterface;

/**
 * Page Type List Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypelistPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares data for Pagetypelist
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeRender()
    {
        if (strtolower($this->runtime_data->route->page_type) === 'list') {
        } else {
            return $this;
        }

        $resource_table_registry = ucfirst(strtolower($this->get('model_name', '', 'runtime_data')))
            . ucfirst(strtolower($this->get('model_type', '', 'runtime_data')));

        /** Get Actual Data for matching to Fields */
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry(
            $this->get('model_type', '', 'runtime_data'),
            $this->get('model_name', '', 'runtime_data'),
            1
        );

        $controller->set('get_customfields', 2);
        $controller->set('use_special_joins', 1);
        $controller->set('check_view_level_access', 1);
        /**
         * $controller->set('model_offset', $this->get('model_offset', 0));
         * $controller->set('model_count', $this->get('model_count', 5));
         * $controller->set('use_pagination', $this->get('model_use_pagination', 1));
         */
        $temp_row = $controller->getData('list');
        /**
         * $controller->set('request_model_type', $this->get('model_type', '', 'runtime_data'));
         * $controller->set('request_model_name', $this->get('model_name', '', 'runtime_data'));
         */
        $controller->set('model_type', 'Dataobject');
        $controller->set('model_name', 'Primary');
        $controller->set('model_query_object', 'list');

        $controller->set('model_type', 'list');
        $controller->set('model_name', 'Primary');

        $this->registry->set('Primary', 'Data', $temp_row);

        return $this;
    }
}
