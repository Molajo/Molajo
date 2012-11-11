<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypegrid;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;
use Molajo\Helpers;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PagetypegridPlugin extends Plugin
{
    /**
     * Prepares data for the Administrator Grid  - run PagetypegridPlugin after AdminmenuPlugin
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
		if (strtolower($this->get('page_type')) == 'grid') {
		} else {
			return true;
		}

        $controllerClass = 'Molajo\\MVC\\Controller\\Controller';
        $connect = new $controllerClass();

        $model_type = $this->get('model_type');
        $model_name = $this->get('model_name');

        $results = $connect->connect($this->get('model_type'), $this->get('model_name'));
        if ($results === false) {
            return false;
        }

        $connect->set('get_customfields', 2);
        $connect->set('use_special_joins', 1);
        $connect->set('process_plugins', 1);

        $this->setToolbar();

        $this->setFilter($connect, $connect->get('primary_prefix'));

        $this->setGrid($connect, $connect->get('primary_prefix'));

        $this->setBatch($connect, $connect->get('primary_prefix'));

//        $this->setForm($model_type, $model_name);

        return true;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return boolean
     * @since  1.0
     */
    protected function setToolbar()
    {
        $url = Services::Registry()->get('Plugindata', 'page_url');

        $button = $this->get('grid_toolbar_buttons');

        if ($button == '#') {
            $button = 'new,edit,publish,feature,archive,checkin,restore,delete,trash,options';
        }

        $grid_toolbar_buttons = explode(',', $button);

        $permissions = Services::Authorisation()
            ->verifyTaskList($grid_toolbar_buttons, $this->get('catalog_id')
        );
        $query_results = array();
        foreach ($grid_toolbar_buttons as $buttonname) {

            if ($permissions[$buttonname] === true) {

                $row = new \stdClass();

                $row->name = Services::Language()->translate(
                    strtoupper('TASK_' . strtoupper($buttonname) . '_BUTTON')
                );
                $row->action = $buttonname;

                if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
                    $row->link = $url . '/' . $row->action;
                } else {
                    $row->link = $url . '&action=' . $row->action;
                }

                $query_results[] = $row;
            }
        }

        if (Services::Registry()->get('Plugindata', 'grid_search', 1) == 1) {
            $row = new \stdClass();
            $row->name = Services::Language()->translate(strtoupper('TASK_' . 'SEARCH' . '_BUTTON'));
            $row->action = 'search';

            if (Services::Registry()->get('Configuration', 'url_sef', 1) == 1) {
                $row->link = $url . '/' . $row->action;
            } else {
                $row->link = $url . '&action=' . $row->action;
            }

            $query_results[] = $row;
        }

        Services::Registry()->set('Plugindata', 'Toolbar', $query_results);

        return true;
    }

    /**
     * Filters: lists stored in registry, where clauses for primary grid query set
     *
     * @param   $connect
     * @param   $primary_prefix
     *
     * @return boolean
     * @since   1.0
     */
    protected function setFilter($connect, $primary_prefix)
    {
        $grid_list = array();
        if ($this->get('grid_list1', '') == '') {
        } else {
            $grid_list[] = $this->get('grid_list1');
        }
        if ($this->get('grid_list2', '') == '') {
        } else {
            $grid_list[] = $this->get('grid_list2');
        }
        if ($this->get('grid_list3', '') == '') {
        } else {
            $grid_list[] = $this->get('grid_list3');
        }
        if ($this->get('grid_list4', '') == '') {
        } else {
            $grid_list[] = $this->get('grid_list4');
        }

        $lists = array();
        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $listname) {

                $items = Services::Text()->getList($listname, $this->parameters);

                if ($items === false) {
                } else {

                    $query_results = Services::Text()->buildSelectlist($listname, $items, 0, 5);

                    Services::Registry()->set('Plugindata', 'list_' . $listname, $query_results);

                    $row = new \stdClass();
                    $row->listname = $listname;
                    $lists[] = $row;
                }
            }
        }

        Services::Registry()->set('Plugindata', 'Gridfilterlist', $lists);

        return true;
    }

    /**
     * Grid Query: results stored in Plugin registry
     *
     * @param   $connect
     * @param   $primary_prefix
     * @param   $table_name
     *
     * @return bool
     * @since   1.0
     */
    protected function setGrid($connect, $primary_prefix)
    {
        $grid_columns = array();
        if ($this->get('grid_column1', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column1');
        }
        if ($this->get('grid_column2', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column2');
        }
        if ($this->get('grid_column3', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column3');
        }
        if ($this->get('grid_column4', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column4');
        }
        if ($this->get('grid_column5', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column5');
        }
        if ($this->get('grid_column6', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column6');
        }
        if ($this->get('grid_column7', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column7');
        }
        if ($this->get('grid_column8', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column8');
        }
        if ($this->get('grid_column9', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column9');
        }
        if ($this->get('grid_column10', '') == '') {
        } else {
            $grid_columns[] = $this->get('grid_column10');
        }

        Services::Registry()->set('Plugindata', 'GridTableColumns', $grid_columns);

        $list = $this->get('criteria_catalog_type_id');

        $connect->model->query->where(
            $connect->model->db->qn($primary_prefix)
                . '.' . $connect->model->db->qn('catalog_type_id')
                . ' IN (' . $list . ')'
        );

        $connect->model->query->where($connect->model->db->qn('catalog.redirect_to_id') . ' = ' . 0);


        $ordering = $this->get('grid_ordering', '');

        if ($ordering == '' || $ordering === null) {
            $ordering = $connect->get('primary_key', 'id');
        }
        Services::Registry()->set('Plugindata', 'GridTableOrdering', $ordering);


        $orderingDirection = $this->get('grid_ordering_direction', 'DESC');

        if ($orderingDirection == 'ASC') {
        } else {
            $orderingDirection = 'DESC';
        }
        Services::Registry()->set('Plugindata', 'GridTableOrderingDirection', $orderingDirection);


        $itemsPerPage = $this->get('grid_items_per_page', 10);

        if ((int)$itemsPerPage == 0) {
            $itemsPerPage = 10;
        }
        Services::Registry()->set('Plugindata', 'GridTableItemsPerPage', $itemsPerPage);

        $connect->set('model_offset', 0);
        $connect->set('model_count', $itemsPerPage);

        $query_results = $connect->getData('list');

        $gridItems = array();

        $name_key = $connect->get('name_key');

        if (count($query_results) > 0) {

            foreach ($query_results as $item) {
                $row = new \stdClass();

                $row = $item;

                $name = $item->$name_key;

                if (isset($item->lvl)) {
                } else {
                    $gridItems = $query_results;
                    break;
                }
                $lvl = (int) $item->lvl - 1;

                if ($lvl > 0) {
                    for ($i = 0; $i < $lvl; $i++) {
                        $name = ' ..' . $name;
                    }
                }
                $row->$name_key = $name;

                $gridItems[] = $row;
            }
        }

        /**
        echo '<pre><br /><br />';
        var_dump($query_results);
        echo '<br /><br /></pre>';
        die;

        echo '<br /><br />';
        echo $connect->model->query->__toString();
        echo '<br /><br />';
*/
        $this->set('model_name', 'Plugindata');
        $this->set('model_type', 'dbo');
        $this->set('model_query_object', 'getPlugindata');
        $this->set('model_parameter', 'PrimaryRequestQueryResults');

        $this->parameters['model_name'] = 'Plugindata';
        $this->parameters['model_type'] = 'dbo';

        Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $query_results);


        return true;
    }

    /**
     * Creates and stores lists for Grid Batch area
     *
     * @param   $connect
     * @param   $primary_prefix
     *
     * @return boolean
     * @since   1.0
     */
    protected function setBatch($connect, $primary_prefix)
    {
        $grid_list = array();

        if (in_array('Status', $grid_list)) {
        } else {
            $grid_list[] = 'Status';
        }
        if (in_array('Categories', $grid_list)) {
        } else {
            $grid_list[] = 'Categories';
        }
        if (in_array('Tags', $grid_list)) {
        } else {
            $grid_list[] = 'Tags';
        }
        if (in_array('Groups', $grid_list)) {
        } else {
            $grid_list[] = 'Groups';
        }

        $names_of_lists = array();

        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $listname) {

                $items = Services::Text()->getList($listname, $this->parameters);

                if ($items === false) {
                } else {

                    if ($listname == 'Status') {
                        $multiple = 0;
                        $size = 0;
                    } else {
                        $multiple = 1;
                        $size = 5;
                    }
                    $query_results = Services::Text()->buildSelectlist($listname, $items, $multiple, $size);

                    Services::Registry()->set('Plugindata', 'listbatch_' . $listname, $query_results);

                    Services::Registry()->set('Plugindata', 'Grid' . $listname, 1);

                    $row = new \stdClass();
                    $row->listname = $listname;
                    $names_of_lists[] = $row;
                }
            }
        }

        return true;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return boolean
     * @since  1.0
     */
    protected function setForm($model_type, $model_name)
    {
        $resource_model_type = $model_type;
        $resource_model_name = $model_name;

        Helpers::Content()->getResourceExtensionParameters(
            (int) $this->parameters['criteria_extension_instance_id']
        );

        /** Array - All Pages in Set
        {{Filters,batch_filters*}}{{Status,batch_status*}}{{Permissions,batch_permissions*}}{{Categories,batch_categories*}}{{Tags, batch_tags}}
         */
        $grid_array = $this->get('grid_array');
        $grid_array = '{{Filters,batch_filters}}{{Status,batch_status}}{{Permissions,batch_permissions}}{{Categories,batch_categories}}{{Tags, batch_tags}}';
        $pages = explode('{{', $grid_array);

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
                $row->title = substr($item, 0, strpos($item, ','));
                $row->url = Services::Registry()->get('Plugindata', 'page_url') . '/page/' . $i;

                $pageArray[] = $row;
            }
        }
        Services::Registry()->set('Plugindata', 'SectionSubmenu', $pageArray);

        /** Even tho links are created to each form page, generate Form for the current page, only */
        //$current_page = '{{' . $pages[$page_number];

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
         * 			page_form_fieldset_handler_view: Formpage
         * 			page_include_parameter: Formpageapplicationbasic
         *
         */
        $connect = Services::Form();

        echo '<pre>';
        var_dump( $grid_array,
            strtolower($this->get('page_type')),
            $resource_model_type,
            $resource_model_name,
            $this->parameters['criteria_extension_instance_id'],
            array());
        echo '</pre>';

        $pageFieldsets = $connect->setPageArray(
            $grid_array,
            strtolower($this->get('page_type')),
            $resource_model_type,
            $resource_model_name,
            $this->parameters['criteria_extension_instance_id'],
            array()
        );

        /** Set the View Model Parameters and Populate the Registry used as the Model */
        $current_page = $this->getPages($pageFieldsets[0]->page_array, $pageFieldsets[0]->page_count);

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

    /**
     * Get Form Page Fieldsets
     *
     * @param $pages
     * @return array
     */
    protected function getPages($pages, $page_count)
    {
        $page_array = array();
        $temp_array = array();
        $temp = explode('}}', $pages);

        foreach ($temp as $set) {
            $set = str_replace(',', ' ', $set);
            $set = str_replace(':', '=', $set);
            $set = str_replace('{{', '', $set);
            $set = str_replace('http=', 'http:', $set);
            if (trim($set) == '') {
            } else {
                $temp_array[] = trim($set);
            }
        }

        $current_page_number = count($temp_array);
        $current_page_number_word = $this->convertNumberToWord($current_page_number);

        foreach ($temp_array as $set) {
            $fields = explode(' ', $set);
            foreach ($fields as $field) {
                $temp = explode('=', $field);
                $pairs[$temp[0]] = $temp[1];
            }

            $row = new \stdClass();
            foreach ($pairs as $key=>$value) {
                $row->$key = $value;
                $row->current_page_number = $current_page_number;
                $row->current_page_number_word = $current_page_number_word;
                $row->total_page_count = $page_count;
            }
            $page_array[] = $row;
        }

        return $page_array;
    }

    /**
     * convertNumberToWord
     *
     * Converts numbers from 1-24 as their respective written word
     *
     * @return string
     * @since   1.0
     */
    public function convertNumberToWord($number)
    {
        $key = $number-1;
        $words = array('one','two','three','four','five','six','seven','eight','nine','ten','eleven','twelve','thirteen','fourteen','fifteen','sixteen','seventeen','eighteen','nineteen','twenty','twentyone','twentytwo','twentythree','twentyfour');
        if (array_key_exists($key, $words)) {
            return $words[$key];
        }

        return false;
    }
}
