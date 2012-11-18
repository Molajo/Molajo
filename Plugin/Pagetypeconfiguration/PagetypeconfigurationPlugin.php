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
     * Prepares Configuration Data
     *
     * @return 	boolean
     * @since	1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type')) == 'configuration') {
        } else {
            return true;
        }

        /** Array - All Pages in Set
        2, {{Access,noformfields}}{{Editor,editor}}{{Grid,grid}}{{Form,form}}{{Item,item}}{{List,list}}
         */
        $temp = $this->get('configuration_array');
        $pages = explode('{{', $temp);

        /** Determine Current Page of Set */
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

        if ($page < count($pages)) {
        } else {
            $page = 1;
        }
        $page_number = $page;

        /** Resource Submenu: Links to various Form Pages (Tabs) - ex. Basic, Metadata, Fields, etc. */
        $pageArray = array();
        $i = 0;
        foreach ($pages as $item) {

            if ($item == '') {
            } else {
                $i++;
                $row = new \stdClass();
                $row->id = $i;
                if ($i == $page_number) {
                    $row->current = 1;
                } else {
                    $row->current = 0;
                }

                $row->id = $i;
                $row->title = substr($item, 0, strpos($item, ','));
                $row->url = Services::Registry()->get('Plugindata', 'page_url') . '/page/' . $i;

                $pageArray[] = $row;
            }
        }
        Services::Registry()->set('Plugindata', 'SectionSubmenu', $pageArray);

        /** Even tho links are created to each form page, generate Form for the current page, only */
        $current_page = '{{' . $pages[$page_number];

        /** Build Fieldsets and Fields */
        $form = Services::Form();

        /** Resource
        1. {{Basic,basic}}
        3. {{Fields,customfields,Customfields}}
        4. {{Editor,editor}}
        5. {{Grid,grid}}
        6. {{Form,form}}
        7. {{Item,item}}
        8. {{List,list}}
         */
        if ($page_number == 1 || $page_number == 3  || $page_number == 4
            || $page_number == 5  || $page_number == 6  || $page_number == 7
            || $page_number == 8) {

            $pageFieldsets = $this->getResourceConfiguration($form, $current_page);
        }

        /** Set the View Model Parameters and Populate the Registry used as the Model */
        $current_page = $form->getPages(
            $pageFieldsets[0]->page_array,
            $pageFieldsets[0]->page_count
        );


        $this->set('model_type', 'Plugindata');
        $this->set('model_name', PRIMARY_QUERY_RESULTS);
        $this->set('model_query_object', 'list');

        $this->parameters['model_type'] = 'Plugindata';
        $this->parameters['model_name'] = PRIMARY_QUERY_RESULTS;

        Services::Registry()->set('Plugindata', PRIMARY_QUERY_RESULTS, $current_page);

        return true;
    }

    /**
     * Prepares Configuration Data
     *
     * @return 	boolean
     * @since	1.0
     */
    protected function getResourceConfiguration($form, $current_page)
    {
        Helpers::Content()->getResourceExtensionParameters(
            (int) $this->parameters['criteria_extension_instance_id']
        );

        /** Set Input */
        $form->set('namespace', strtolower($this->get('page_type')));

        $form->set('model_type', $this->get('model_type'));
        $form->set('model_name', $this->get('model_name'));
        $form->set('model_registry_name',
            ucfirst(strtolower($this->get('model_name'))) . ucfirst(strtolower($this->get('model_type')))
        );

        $form->set('extension_instance_id', $this->get('criteria_extension_instance_id'));

        $form->set('data', array());

        /** Parameters */
        $parameters = Services::Registry()->getArray('ResourcesSystemParameters');
        $array2 = Services::Registry()->getArray('Parameters');

        foreach ($array2 as $key => $value) {
            if (substr($key, 0, strlen('configuration')) == 'configuration') {
                $parameters[$key] = $value;
            }
        }

        $form->set('parameters', $parameters);
        $form->set('parameter_fields', Services::Registry()->get('ResourcesSystem', 'parameters'));

        /** Metadata */
        $form->set('metadata', Services::Registry()->getArray('ResourcesSystemMetadata'));
        $form->set('metadata_fields', Services::Registry()->get('ResourcesSystem', 'metadata'));

        /** Customfields */
        $form->set('customfields', Services::Registry()->getArray('ResourcesSystemCustomfields'));
        $form->set('customfields_fields', Services::Registry()->get('ResourcesSystem', 'customfields'));

        /** Build Fieldsets and Fields */
        return $form->execute($current_page);
    }
}
