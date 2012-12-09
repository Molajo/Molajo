<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
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
		if (strtolower($this->get('page_type', '', 'parameters')) == QUERY_OBJECT_LIST) {
		} else {
			return true;
		}

        $resource_table_registry = ucfirst(strtolower($this->get('model_name', '', 'parameters')))
            . ucfirst(strtolower($this->get('model_type', '', 'parameters')));

        /** Get Actual Data for matching to Fields */
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry($this->get('model_type', '', 'parameters'), $this->get('model_name', '', 'parameters'));
        $controller->setDataobject();
        $controller->connectDatabase();

        $controller->set('get_customfields', 2, 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('check_view_level_access', 1, 'model_registry');
/**
        $controller->set('model_offset', $this->get('model_offset', 0), 'model_registry');
        $controller->set('model_count', $this->get('model_count', 5), 'model_registry');
        $controller->set('use_pagination', $this->get('model_use_pagination', 1));
*/
        $temp_query_results = $controller->getData(QUERY_OBJECT_LIST);
/**
        $controller->set('request_model_type', $this->get('model_type', '', 'parameters'), 'model_registry');
        $controller->set('request_model_name', $this->get('model_name', '', 'parameters'), 'model_registry');
*/
        $controller->set('model_type', DATA_OBJECT_LITERAL, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');
        $controller->set('model_query_object', QUERY_OBJECT_LIST, 'model_registry');

        $controller->set('model_type', QUERY_OBJECT_LIST, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');

        Services::Registry()->set(PRIMARY_LITERAL, DATA_LITERAL, $temp_query_results);

        return true;
    }

    /**
     * Before the Query results are injected into the View
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeRenderView()
    {
        if (strtolower($this->get('page_type', '', 'parameters')) == QUERY_OBJECT_LIST
            || strtolower($this->get('page_type', '', 'parameters')) == 'grid'
        ) {
        } else {
            return true;
        }

        if ((int) $this->get('total_rows', 0, 'parameters') == 0
            || $this->row === false
            || $this->row == null
        ) {
            return true;
        }

        if (is_object($this->row)) {
        } else {
            return true;
        }

        /** first row */
        if ($this->get('row_count', 0, 'parameters') == 1) {
            $value = 'first';
        } else {
            $value = '';
        }
        $this->saveField(null, 'first_row', $value);

        /** last row */
        if ($this->get('row_count', 0, 'parameters') == $this->get('total_rows', 0, 'parameters')) {
            $value = 'last';
        } else {
            $value = '';
        }
        $this->saveField(null, 'last_row', $value);

        /** total_rows */
        $this->saveField(null, 'total_rows', $this->get('total_rows', 0, 'parameters'));

        /** even_or_odd_row */
        $this->saveField(null, 'even_or_odd_row', $this->get('even_or_odd', 0, 'parameters'));

        /** grid_row_class */
        $value = ' class="' .
            trim(trim($this->row->first_row)
                    . ' ' . trim($this->row->even_or_odd_row)
                    . ' ' . trim($this->row->last_row))
            . '"';

        $this->saveField(null, 'grid_row_class', $value);

        return true;
    }
}
