<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypeapplication;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypeapplicationPlugin extends Plugin
{
    /**
     * Prepares Configuration Data
     *
     * @return 	boolean
     * @since	1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type')) == 'application') {
        } else {
            return true;
        }

        $resource_model_type = $this->get('model_type');
        $resource_model_name = $this->get('model_name');

        Helpers::Content()->getResourceExtensionParameters(
            (int) $this->parameters['criteria_extension_instance_id']
        );

        /** Array - All Pages in Set
        {{Basic,basic}}{{User,user}}{{Media,media}}{{System,system}}{{Views,views}}{{Form,form}}{{Item,item}}{{List,list}}
         */
        $temp = $this->get('application_array');

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

        /** Submenu: Links to various Form Pages (Tabs) - ex. Basic, Metadata, Fields, etc. */
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

        /**
         * $pageFieldsets - contains two fields: page_count and page_array
         *
         * 	page_count - the number of pages created (will be 1 for this use)
         *
         * 	page_array: several fields that will be used by the primary view to display titles
         *		and create the include that contains the form fieldsets
         *
         *	Example page_array: Basic Page (tab 1)
         * 		page_title: Basic
         * 		page_title_extended: Articles Basic Configuration
         * 		page_namespace: application
         * 		page_link: applicationbasic
         *
         * 		Form View to include and the Registry containing Form contents:
         * 			fieldset_template_view: Formpage
         * 			fieldset_template_view_parameter: Formpageapplicationbasic
         *
         */
        $form = Services::Form();

        $pageFieldsets = $form->setPageArray(
            $current_page,
            strtolower($this->get('page_type')),
            $resource_model_type,
            $resource_model_name,
            $this->parameters['criteria_extension_instance_id'],
            array()
        );

        /** Set the View Model Parameters and Populate the Registry used as the Model */
        $current_page = $form->getPages($pageFieldsets[0]->page_array, $pageFieldsets[0]->page_count);

        $this->set('model_name', 'Plugindata');
        $this->set('model_type', 'dbo');
        $this->set('model_query_object', 'getPlugindata');
        $this->set('model_parameter', 'PrimaryRequestQueryResults');

        $this->parameters['model_name'] = 'Plugindata';
        $this->parameters['model_type'] = 'dbo';

        Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $current_page);

//        echo '<pre>';
//        var_dump(Services::Registry()->get('Plugindata', 'Formpageapplicationbasic'));
//        echo '</pre>';
        return true;
    }

}
