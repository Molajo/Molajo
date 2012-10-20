<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Menuitemtypedashboard;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MenuitemtypedashboardPlugin extends Plugin
{
	/**
	 * Prepares data for Menuitemtypedashboard
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeParse()
	{
		if (APPLICATION_ID == 2) {
		} else {
			return true;
		}

		if (strtolower($this->get('template_view_path_node')) == 'admindashboard') {
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

		/** Tab Group Class */
		$tab_class = $this->get('dashboard_tab_class');

		/** Create Tabs */
		$namespace = 'Menuitemtypedashboard';

		$tab_array = $this->get('dashboard_tab_array');

		$tabs = Services::Form()->setTabArray(
			$this->get('model_type'),
			$this->get('model_name'),
			$namespace,
			$tab_array,
			'dahboard_tab_',
			'Admindashboard',
			'Admindashboardtab',
			$tab_class,
			null,
			null
		);

		$this->set('model_name', 'Plugindata');
		$this->set('model_type', 'dbo');
		$this->set('model_query_object', 'getPlugindata');
		$this->set('model_parameter', 'Menuitemtypedashboard');

		$this->parameters['model_name'] = 'Plugindata';
		$this->parameters['model_type'] = 'dbo';

		Services::Registry()->set('Plugindata', 'Menuitemtypedashboard', $tabs);

		/**
		echo '<pre>';
		var_dump($tabs);
		echo '</pre>';
		 */

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
 * @param   $connect
 * @param   $primary_prefix
 *
 * @return boolean
 * @since   1.0
 */
protected function setOptions()
{

	$list = Services::Text()->getList('Portlets', $this->parameters);

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
