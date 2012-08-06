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

        return true;
    }
}
