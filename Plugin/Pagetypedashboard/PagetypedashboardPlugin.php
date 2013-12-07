<?php
/**
 * Page Type Dashboard Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagetypedashboard;

use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Page Type Dashboard Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class PagetypedashboardPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * Prepares data for Pagetypedashboard
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeParse()
    {
        return $this;
        if (strtolower($this->runtime_data->route->page_type) == 'dashboard') {
        } else {
            return $this;
        }

        $portletOptions = $this->registry->get('runtime_data', 'dashboard_portlet');
        if (trim($portletOptions) == '') {
            return $this;
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
            $this->get('model_type', '', 'runtime_data'),
            $this->get('model_name', '', 'runtime_data'),
            $namespace,
            $page_array,
            'dahboard_page_',
            'Pagetypedashboard',
            'Pagetypedashboardtab',
            null,
            null
        );

        $controller->set('request_model_type', $this->get('model_type', '', 'runtime_data'));
        $controller->set('request_model_name', $this->get('model_name', '', 'runtime_data'));

        $controller->set('model_type', 'Dataobject');
        $controller->set('model_name', 'Primary');
        $controller->set('model_query_object', 'list');

        $controller->set('model_type', 'list');
        $controller->set('model_name', 'Primary');

        $this->registry->set(
            'Primary',
            'Data',
            $tabs
        );

        return $this;
    }

    /**
     * Portlets
     *
     * @param   array $portletOptionsArray
     *
     * @return  $this
     * @since   1.0
     */
    public function portlets(array $portletOptionsArray = array())
    {
        $i               = 1;
        $portletIncludes = '';
        foreach ($portletOptionsArray as $portlet) {

            $portletIncludes .= '<include '
                . ucfirst(strtolower(trim($portlet)))
                . ' wrap=Portlet wrap_id=portlet'
                . $i
                . ' wrap_class=portlet/>'
                . chr(13);

            $i ++;
        }

        $this->registry->set('xxxx', 'PortletOptions', $portletIncludes);

        if ($this->get('model_type', '', 'runtime_data') == '' || $this->get('model_name', '', 'runtime_data') == '') {
            return $this;
        }

        $this->setOptions();
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return  $this
     * @since  1.0
     */
    protected function setDashboardPermissions()
    {
    }

    /**
     * Options: creates a list of Portlets available for this Dashboard
     *
     * @return  $this
     * @since   1.0
     */
    protected function setOptions()
    {
        $results = Services::Text()->getDatalist('Portlets', 'Datalist', $this->runtime_data);
        if ($results === false) {
            return $this;
        }

        if (isset($this->runtime_data->selected)) {
            $selected = $this->runtime_data->selected;
        } else {
            $selected = null;
        }

        $list = Services::Text()->buildSelectList(
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
            $temp_row->value    = $this->language_controller->translate(
                ucfirst(strtolower(substr($item->value, 7, strlen($item->value))))
            );
            $temp_row->selected = '';
            $temp_row->multiple = '';
            $temp_row->listname = 'Portlets';

            $temp_query_results[] = $temp_row;
        }

        $this->runtime_data->plugin_data->datalists->portlets = $temp_query_results;

        return $this;
    }
}
