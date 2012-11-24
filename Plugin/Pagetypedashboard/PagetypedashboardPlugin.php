<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypedashboard;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
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
		if (strtolower($this->get('page_type')) == 'dashboard') {
		} else {
			return true;
		}

        $portletOptions = Services::Registry()->get('Parameters', 'dashboard_portlet');
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
            $this->get('model_type'),
            $this->get('model_name'),
            $namespace,
            $page_array,
            'dahboard_page_',
            'Pagetypedashboard',
            'Pagetypedashboardtab',
            null,
            null
        );

        $this->set('request_model_type', $this->get('model_type'));
        $this->set('request_model_name', $this->get('model_name'));

        $this->set('model_type', DATAOBJECT_MODEL_TYPE);
        $this->set('model_name', PRIMARY_QUERY_RESULTS_MODEL_NAME);
        $this->set('model_query_object', QUERY_OBJECT_LIST);

        $this->parameters['model_type'] = DATAOBJECT_MODEL_TYPE;
        $this->parameters['model_name'] = PRIMARY_QUERY_RESULTS_MODEL_NAME;

        Services::Registry()->set(
            PRIMARY_QUERY_RESULTS_MODEL_NAME,
            PRIMARY_QUERY_RESULTS_MODEL_NAME_RESULTS,
            $tabs
        );


        return true;
    }

    public function portlets($portletOptionsArray)
    {
        $i = 1;
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

        Services::Registry()->set('Plugindata', 'PortletOptions', $portletIncludes);

        if ($this->get('model_type') == '' || $this->get('model_name') == '') {
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

    $results = Services::Text()->getList('Portlets', $this->parameters);

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

    $query_results = array();

    foreach ($list as $item) {

        $row = new \stdClass();
        $row->id = $item->id;
        $row->value = Services::Language()->translate(
            ucfirst(strtolower(substr($item->value, 7, strlen($item->value))))
        );
        $row->selected = '';
        $row->multiple = '';
        $row->listname = 'Portlets';

        $query_results[] = $row;
    }
    Services::Registry()->set('Plugindata', 'list_portlets', $query_results);

    return true;
}
}
