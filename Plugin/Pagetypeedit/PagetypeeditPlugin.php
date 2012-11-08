<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeedit;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeeditPlugin extends Plugin
{
    /**
     * Prepares data for Pagetypeedit
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type')) == 'edit') {
        } else {
            return true;
        }

        /** Item Data */
        $item = Services::Registry()->get('Plugindata', 'PrimaryRequestQueryResults');

        /** Resource Configuration */
        Helpers::Content()->getResourceMenuitemParameters('Configuration', $this->get('criteria_extension_instance_id'));

        /** Namespace */
        $namespace = 'Pagetypeedit';

        $page_array = Services::Registry()->get('ConfigurationMenuitemParameters', 'editor_page_array');
        $item[0]->page_array = $page_array;

        $tabs = Services::Form()->setPageArray(
            $this->get('model_type'),
            $this->get('model_name'),
            $namespace,
            $page_array,
            'editor_page_',
            'Pagetypeedit',
            'Pagetypeedittab',
            $this->get('extension_instance_id'),
            $item
        );

        $this->set('model_name', 'Plugindata');
        $this->set('model_type', 'dbo');
        $this->set('model_query_object', 'getPlugindata');
        $this->set('model_parameter', 'Pagetypeedit');

        $this->parameters['model_name'] = 'Plugindata';
        $this->parameters['model_type'] = 'dbo';

        Services::Registry()->set('Plugindata', 'Pagetypeedit', $tabs);

        Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $item);

        /**
        echo '<pre>';
        var_dump($tabs);
        echo '</pre>';

        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata', 'PagetypeeditPagetypeeditmain'));
        echo '</pre>';

        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata', 'PagetypeeditPagetypeeditpublish'));
        echo '</pre>';

        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata', 'PagetypeeditPagetypeeditpermissions'));
        echo '</pre>';

        echo '<pre>';
        var_dump(Services::Registry()->get('Plugindata', 'PagetypeeditPagetypeeditseo'));
        echo '</pre>';
        */

        return true;
    }
}
