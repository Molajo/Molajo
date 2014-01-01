<?php
/**
 * Page Type List Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypelist;

use Molajo\Plugin\DisplayEventPlugin;
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
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->runtime_data->route->page_type) == 'list') {
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
        $temp_query_results = $controller->getData('list');
        /**
         * $controller->set('request_model_type', $this->get('model_type', '', 'runtime_data'));
         * $controller->set('request_model_name', $this->get('model_name', '', 'runtime_data'));
         */
        $controller->set('model_type', 'Dataobject');
        $controller->set('model_name', 'Primary');
        $controller->set('model_query_object', 'list');

        $controller->set('model_type', 'list');
        $controller->set('model_name', 'Primary');

        $this->registry->set('Primary', 'Data', $temp_query_results);

        return $this;
    }

    /**
     * Before the Query results are injected into the View
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRenderView()
    {

        return $this;
        if (strtolower($this->runtime_data->route->page_type) == 'list'
            || strtolower($this->runtime_data->route->page_type) == 'grid'
        ) {
        } else {
            return $this;
        }

        if ((int)$this->get('total_rows', 0, 'runtime_data') == 0
            || $this->query_results === false
            || $this->query_results == null
        ) {
            return $this;
        }

        if (is_object($this->query_results)) {
        } else {
            return $this;
        }

        /** first row */
        if ($this->get('row_count', 0, 'runtime_data') == 1) {
            $value = 'first';
        } else {
            $value = '';
        }
        $this->setField(null, 'first_row', $value);

        /** last row */
        if ($this->get('row_count', 0, 'runtime_data') == $this->get('total_rows', 0, 'runtime_data')) {
            $value = 'last';
        } else {
            $value = '';
        }
        $this->setField(null, 'last_row', $value);

        /** total_rows */
        $this->setField(null, 'total_rows', $this->get('total_rows', 0, 'runtime_data'));

        /** even_or_odd_row */
        $this->setField(null, 'even_or_odd_row', $this->get('even_or_odd', 0, 'runtime_data'));

        /** grid_row_class */
        $value = ' class="' .
            trim(
                trim($this->query_results->first_row)
                . ' ' . trim($this->query_results->even_or_odd_row)
                . ' ' . trim($this->query_results->last_row)
            )
            . '"';

        $this->setField(null, 'grid_row_class', $value);

        return $this;
    }
}
