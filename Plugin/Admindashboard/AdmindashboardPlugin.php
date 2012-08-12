<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Admindashboard;

use Molajo\Extension\Plugin\Content\ContentPlugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdmindashboardPlugin extends ContentPlugin
{
    /**
     * Before-read processing
     *
     * Prepares data for the Administrator Grid  - position AdmindashboardPlugin last
     *
     * @return void
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

		$portletOptions = Services::Registry()->get('Parameters', 'dashboard');

		if (trim($portletOptions) == '') {
			return true;
		}

		$portletOptionsArray = explode(',', $portletOptions);

		if (count($portletOptionsArray) == 0
			|| $portletOptionsArray == false) {
			return true;
		}

		$i = 1;
		$portletIncludes = '';
		foreach ($portletOptionsArray as $portlet) {

			$portletIncludes .= '<include:template name='
				. ucfirst(strtolower(trim($portlet)))
				. ' wrap=section wrap_id=portlet'
				. $i
				. ' wrap_class=portlet/>'
				. chr(13);

			$i++;
		}

		Services::Registry()->set('Plugindata', 'PortletOptions', $portletIncludes);

		$this->setOptions();

        return true;
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

		if (count($list) == 0 || $list == false) {
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
