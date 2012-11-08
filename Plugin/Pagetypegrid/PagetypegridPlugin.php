<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypegrid;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

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

        /** Namespace */
        $namespace = $this->parameters['grid_page_link_namespace'];
        $namespace = ucfirst(strtolower($namespace));

        /** Create Tab Array */
        $page_array = '';
        $batch = array();
        $batch = Services::Registry()->get('Parameters', 'grid_batch_*');

        if (count($batch) == 0 || $batch === false) {
        } else {
            /** Need filters first (and it is not alphabetical but parameters are sorted that way) */
            foreach ($batch as $key => $value) {
                if ((int) $value === 1) {
                    if ($key == 'grid_batch_filters') {
                        $temp = '{{';
                        $temp .= ucfirst(strtolower(substr($key, strlen('grid_batch_'), 9999))) . ',';
                        $temp .= strtolower(substr($key, strlen('grid_batch_'), 9999)) . '}}';
                        $page_array .= $temp;
                    }
                }
            }
            /** Now, pick up all none-filters in alphabetic order, hackish? Sure, but I am a hacker. */
            foreach ($batch as $key => $value) {
                if ((int) $value === 1) {
                    if ($key == 'grid_batch_filters') {
                    } else {
                        $temp = '{{';
                        $temp .= ucfirst(strtolower(substr($key, strlen('grid_batch_'), 9999))) . ',';
                        $temp .= strtolower(substr($key, strlen('grid_batch_'), 9999)) . '}}';
                        $page_array .= $temp;
                    }
                }
            }
        }

        $tabs = Services::Form()->setPageArray(
            $this->get('model_type'),
            $this->get('model_name'),
            $namespace,
            $page_array,
            null,
            'Admin',
            'Admingridtab',
            null,
            array()
        );

        Services::Registry()->set('Plugindata', 'Admingrid', $tabs);

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

        $grid_toolbar_buttons = explode(',', $this->get('grid_toolbar_buttons',
                'new,edit,publish,feature,archive,checkin,restore,delete,trash,options')
        );

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

        Services::Registry()->set('Plugindata', 'Admingridfilterlist', $lists);

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

        Services::Registry()->set('Plugindata', 'AdminGridTableColumns', $grid_columns);

        $list = $this->get('criteria_catalog_type_id');

        $connect->model->query->where(
            $connect->model->db->qn($primary_prefix)
                . '.' . $connect->model->db->qn('catalog_type_id')
                . ' IN (' . $list . ')'
        );

        $connect->model->query->where($connect->model->db->qn('catalog.redirect_to_id') . ' = ' . 0);

        $ordering = $this->get('grid_ordering', 'start_publishing_datetime');

        if ($ordering == '' || $ordering === null) {
            $ordering = $connect->get('primary_key', 'id');
        }
        Services::Registry()->set('Plugindata', 'AdminGridTableOrdering', $ordering);

        $connect->model->query->order($connect->model->db->qn($ordering));

        $connect->set('model_offset', 0);
        $connect->set('model_count', 20);

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

                    Services::Registry()->set('Plugindata', 'AdminGrid' . $listname, 1);

                    $row = new \stdClass();
                    $row->listname = $listname;
                    $names_of_lists[] = $row;
                }
            }
        }

        return true;
    }
}
