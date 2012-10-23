<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Menuitemtypeconfiguration;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class MenuitemtypeconfigurationPlugin extends Plugin
{
    /**
     * Prepares Configuration Tabs and Tab Content
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

        if (strtolower($this->get('template_view_path_node')) == 'adminconfiguration') {
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

        $tab_array = $this->get('configuration_tab_array');

        $query_results = Services::Form()->setTabArray(
            $resource_model_type,
            $resource_model_name,
            $namespace,
            $tab_array,
            'configuration_tab_',
            'Adminconfiguration',
            'Adminconfigurationtab',
            $tab_class,
            $this->get('extension_instance_id'),
            array()
        );

		$this->set('model_name', 'Plugindata');
        $this->set('model_type', 'dbo');
        $this->set('model_query_object', 'getPlugindata');
        $this->set('model_parameter', 'PrimaryRequestQueryResults');

        $this->parameters['model_name'] = 'Plugindata';
        $this->parameters['model_type'] = 'dbo';

        Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $query_results);

        return true;
    }
}
