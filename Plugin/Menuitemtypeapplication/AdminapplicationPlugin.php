<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Menuitemtypeapplication;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class AdminapplicationPlugin extends Plugin
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

        if (strtolower($this->get('template_view_path_node')) == 'adminapplication') {
        } else {
            return true;
        }

        /** Tab Group Class */
        $tab_class = str_replace(',', ' ', $this->get('configuration_tab_class'));

        /** Create Tabs */
        $namespace = $this->parameters['application_tab_link_namespace'];
        $namespace = ucfirst(strtolower($namespace));

        $tab_array = $this->parameters['application_tab_array'];

        $tabs = Services::Form()->setTabArray(
            'Table',
            'Application',
            $namespace,
            $tab_array,
            'application_tab_',
            'Adminapplication',
            'Admineapplicationtab',
            $tab_class,
            null,
            array()
        );

        Services::Registry()->set('Plugindata', 'Menuitemtypeapplication', $tabs);

        return true;
    }
}
