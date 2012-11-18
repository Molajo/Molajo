<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypelist;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypelistPlugin extends Plugin
{
    /**
     * Prepares data for Pagetypelist
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
		if (strtolower($this->get('page_type')) == 'list') {
		} else {
			return true;
		}

        $resource_table_registry = ucfirst(strtolower($this->get('model_name')))
            . ucfirst(strtolower($this->get('model_type')));

        /** Get Actual Data for matching to Fields */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $results = $controller->getModelRegistry($this->get('model_type'), $this->get('model_name'));
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('get_customfields', 2);
        $controller->set('use_special_joins', 1);
        $controller->set('check_view_level_access', 1);

        $controller->set('model_offset', $this->get('model_offset', 0));
        $controller->set('model_count', $this->get('model_count', 5));
        $controller->set('use_pagination', $this->get('model_use_pagination', 1));

        $list = $controller->getData(QUERY_OBJECT_LIST);

        Services::Registry()->set('Plugindata', PRIMARY_QUERY_RESULTS, $list);

        $this->set('model_type', 'Plugindata');
        $this->set('model_name', PRIMARY_QUERY_RESULTS);
        $this->set('model_query_object', 'list');

        $this->parameters['model_type'] = 'Plugindata';
        $this->parameters['model_name'] = PRIMARY_QUERY_RESULTS;

        return true;
    }

    /**
     * Before the Query results are injected into the View
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeViewRender()
    {
        if (strtolower($this->get('page_type')) == 'list'
            || strtolower($this->get('page_type')) == 'grid'
        ) {
        } else {
            return true;
        }

        if ((int) $this->parameters['total_rows'] == 0
            || $this->data === false
            || $this->data == null
        ) {
            return true;
        }

        if (is_object($this->data)) {
        } else {
            return true;
        }

        /** first row */
        if ($this->parameters['row_count'] == 1) {
            $value = 'first';
        } else {
            $value = '';
        }
        $this->saveField(null, 'first_row', $value);

        /** last row */
        if ($this->parameters['row_count'] == $this->parameters['total_rows']) {
            $value = 'last';
        } else {
            $value = '';
        }
        $this->saveField(null, 'last_row', $value);

        /** total_rows */
        $this->saveField(null, 'total_rows', $this->parameters['total_rows']);

        /** even_or_odd_row */
        $this->saveField(null, 'even_or_odd_row', $this->parameters['even_or_odd']);

        /** grid_row_class */
        $value = ' class="' .
            trim(trim($this->data->first_row)
                    . ' ' . trim($this->data->even_or_odd_row)
                    . ' ' . trim($this->data->last_row))
            . '"';

        $this->saveField(null, 'grid_row_class', $value);

        return true;
    }
}
