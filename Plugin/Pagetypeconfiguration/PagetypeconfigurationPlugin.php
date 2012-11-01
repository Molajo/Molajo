<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeconfiguration;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeconfigurationPlugin extends Plugin
{
    /**
     * Prepares Configuration Tabs and Tab Content
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type')) == 'configuration') {
        } else {
            return true;
        }

        $resource_model_type = $this->get('model_type');
        $resource_model_name = $this->get('model_name');

        /** Retrieve Resource Parameters  */
        Helpers::Content()->getResourceExtensionParameters((int) $this->parameters['criteria_extension_instance_id']);

        /** Tab Group Class */
        $tab_class = str_replace(',', ' ', $this->get('configuration_tab_class'));

        /** Create Tabs */
        $namespace = $this->get('configuration_tab_link_namespace');
        $namespace = ucfirst(strtolower($namespace));

		$temp = $this->get('configuration_tab_array');
		$tabs = explode('{{', $temp);

		/** Determine Current Page Number */
		$temp = Services::Registry()->get('Parameters', 'request_filters', array());
		$filters = explode(',', $temp);

		$page = 1;
		if ($filters == '' || count($filters) == 0) {
			$page = 1;
		} else {
			foreach ($filters as $x) {
				if (trim($x) == '') {
				} else {
					$pair = explode(':', $x);
					if (strtolower($pair[0]) == 'page') {
						$page = (int) $pair[1];
						break;
					}
				}
			}
		}

		if ($page < count($tabs)) {
		} else {
			$page = 1;
		}

		$tab_array = '{{' . $tabs[$page];

        $query_results = Services::Form()->setTabArray(
            $resource_model_type,
            $resource_model_name,
            $namespace,
            $tab_array,
            'configuration_tab_',
            'Adminconfiguration',
            'Adminconfigurationtab',
            $tab_class,
            $this->get('criteria_extension_instance_id'),
            array()
        );

		$query_results[0]->tab_count = count($tabs) - 1;

		$this->set('model_name', 'Plugindata');
        $this->set('model_type', 'dbo');
        $this->set('model_query_object', 'getPlugindata');
        $this->set('model_parameter', 'PrimaryRequestQueryResults');

        $this->parameters['model_name'] = 'Plugindata';
        $this->parameters['model_type'] = 'dbo';

        Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $query_results);

		/** Build Tabs */
		$pageArray = array();
		$i = 0;
		foreach ($tabs as $tab) {
			if ($tab == '') {
			} else {
				$i++;
				$row = new \stdClass();
				$row->id = $i;
				if ($i == $page) {
					$row->current = 1;
				} else {
					$row->current = 0;
				}
				$row->title = substr($tab, 0, strpos($tab, ','));
				$row->url = Services::Registry()->get('Plugindata', 'page_url') . '/page/' . $i;

				$pageArray[] = $row;
			}
		}
		Services::Registry()->set('Plugindata', 'Submenu', $pageArray);

		return true;
    }
}
