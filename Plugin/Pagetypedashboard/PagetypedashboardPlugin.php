<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Pagetypedashboard;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class PagetypedashboardPlugin extends Plugin
{
    /**
     * Prepares data for Pagetypedashboard
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type', '', 'parameters')) == 'dashboard') {
        } else {
            return true;
        }

        $portletOptions = Services::Registry()->get('parameters', 'dashboard_portlet');
        if (trim($portletOptions) == '') {
            return true;
        }

        $portletOptionsArray = explode(',', $portletOptions);

        if (count($portletOptionsArray) == 0
            || $portletOptionsArray === false
        ) {
        } else {
            $this->portlets($portletOptionsArray);
        }

        /** Create Tabs */
        $namespace = 'Pagetypedashboard';

        $page_array = $this->get('dashboard_page_array');

        $tabs = Services::Form()->setPageArray(
            $this->get('model_type', '', 'parameters'),
            $this->get('model_name', '', 'parameters'),
            $namespace,
            $page_array,
            'dahboard_page_',
            'Pagetypedashboard',
            'Pagetypedashboardtab',
            null,
            null
        );

        $controller->set('request_model_type', $this->get('model_type', '', 'parameters'), 'model_registry');
        $controller->set('request_model_name', $this->get('model_name', '', 'parameters'), 'model_registry');

        $controller->set('model_type', DATA_OBJECT_LITERAL, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');
        $controller->set('model_query_object', QUERY_OBJECT_LIST, 'model_registry');

        $controller->set('model_type', QUERY_OBJECT_LIST, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');

        Services::Registry()->set(
            PRIMARY_LITERAL,
            DATA_LITERAL,
            $tabs
        );


        return true;
    }

    public function portlets($portletOptionsArray)
    {
        $i               = 1;
        $portletIncludes = '';
        foreach ($portletOptionsArray as $portlet) {

            $portletIncludes .= '<include:template name='
                . ucfirst(strtolower(trim($portlet)))
                . ' wrap=Portlet wrap_id=portlet'
                . $i
                . ' wrap_class=portlet/>'
                . chr(13);

            $i++;
        }

        Services::Registry()->set('xxxx', 'PortletOptions', $portletIncludes);

        if ($this->get('model_type', '', 'parameters') == '' || $this->get('model_name', '', 'parameters') == '') {
            return true;
        }

        $this->setOptions();
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return boolean
     * @since  1.0
     */
    protected function setDashboardPermissions()
    {
    }

    /**
     * Options: creates a list of Portlets available for this Dashboard
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setOptions()
    {
        $results = Services::Text()->getDatalist('Portlets', DATALIST_LITERAL, $this->parameters);
        if ($results === false) {
            return true;
        }

        if (isset($this->parameters['selected'])) {
            $selected = $this->parameters['selected'];
        } else {
            $selected = null;
        }

        $list = Services::Text()->buildSelectlist(
            'Portlets',
            $results[0]->listitems,
            $results[0]->multiple,
            $results[0]->size,
            $selected
        );

        if (count($list) == 0 || $list === false) {
            //throw exception
        }

        $temp_query_results = array();

        foreach ($list as $item) {

            $temp_row           = new \stdClass();
            $temp_row->id       = $item->id;
            $temp_row->value    = Services::Language()->translate(
                ucfirst(strtolower(substr($item->value, 7, strlen($item->value))))
            );
            $temp_row->selected = '';
            $temp_row->multiple = '';
            $temp_row->listname = 'Portlets';

            $temp_query_results[] = $temp_row;
        }
        Services::Registry()->set(DATALIST_LITERAL, 'Portlets', $temp_query_results);

        return true;
    }
}
