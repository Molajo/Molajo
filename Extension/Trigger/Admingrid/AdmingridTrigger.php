<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Trigger\Admingrid;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class AdmingridTrigger extends ContentTrigger
{
    /**
     * Prepares data for the Administrator Grid  - position AdmingridTrigger last
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('template_view_path_node')) == 'admingrid') {
        } else {
            return true;
        }

        $controllerClass = 'Molajo\\Controller\\ReadController';
        $connect = new $controllerClass();

        $results = $connect->connect($this->parameters['model_type'], $this->parameters['model_name']);
        if ($results == false) {
            return false;
        }
        $connect->set('get_customfields', 2);
        $connect->set('use_special_joins', 1);

        $this->table_registry_name = ucfirst(strtolower($this->parameters['model_name']))
            . ucfirst(strtolower($this->parameters['model_type']));

		if (Services::Registry()->get('Configuration', 'profiler_output_queries_table_registry', 0) == 1) {

            $profiler_message = 'AdmingridTrigger Model Type ' . $this->parameters['model_type']
                . ' Model Name ' . $this->parameters['model_name']
                . ' Table Registry ' . $this->table_registry_name . ' contents follow:';

            ob_start();
            echo '<br /><br />';
            echo '<pre>';
            Services::Registry()->get($this->table_registry_name, '*');
            echo '</pre><br /><br />';

            $profiler_message .= ob_get_contents();
            ob_end_clean();

			Services::Profiler()->set($profiler_message, LOG_OUTPUT_QUERIES, VERBOSE);
		}

        $this->setToolbar();
        $this->setFilter($connect, $connect->get('primary_prefix'));
        $this->setGrid($connect, $connect->get('primary_prefix'), $connect->get('table_name'));
        $this->setPagination();
        $this->setBatch($connect, $connect->get('primary_prefix'));

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
        $url = Services::Registry()->get('Parameters', 'full_page_url');

        $grid_toolbar_buttons = explode(',', $this->get('grid_toolbar_buttons',
                'new,edit,publish,feature,archive,checkin,restore,delete,trash,options')
        );

        $permissions = Services::Authorisation()
            ->verifyTaskList($grid_toolbar_buttons, $this->get('extension_catalog_id')
        );

        $query_results = array();

        foreach ($grid_toolbar_buttons as $buttonname) {

            if ($permissions[$buttonname] == true) {

                $row = new \stdClass();
                $row->name = Services::Language()->translate(strtoupper('TASK_' . strtoupper($buttonname) . '_BUTTON'));
                $row->action = $buttonname;
                $row->link = $url . '&action=' . $row->action;

                $query_results[] = $row;
            }
        }

        if (Services::Registry()->get('Triggerdata', 'grid_search', 1) == 1) {
            $row = new \stdClass();
            $row->name = Services::Language()->translate(strtoupper('TASK_' . 'SEARCH' . '_BUTTON'));
            $row->action = 'search';
            $row->link = $url . '&action=search';

            $query_results[] = $row;
        }

        Services::Registry()->set('Triggerdata', 'AdminToolbar', $query_results);

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
        $grid_list = explode(',', $this->get('grid_lists', 'Author,Tags,Status'));

        $lists = array();
        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $listname) {

                $items = Services::Text()->getList($listname, $this->parameters);

                if ($items == false) {
                } else {

                    $query_results = Services::Text()->buildSelectlist($listname, $items, 0, 5);

                    Services::Registry()->set('Triggerdata', 'list_' . $listname, $query_results);

                    $row = new \stdClass();
                    $row->listname = $listname;
                    $lists[] = $row;
                }
            }
        }

        Services::Registry()->set('Triggerdata', 'GridFilters', $lists);

        return true;
    }

    /**
     * Grid Query: results stored in Trigger registry
     *
     * @param   $connect
     * @param   $primary_prefix
     * @param   $table_name
     *
     * @return bool
     * @since   1.0
     */
    protected function setGrid($connect, $primary_prefix, $table_name)
    {
        $grid_columns = explode(',', $this->get('grid_columns',
                'title,created_by,start_publishing_datetime,ordering')
        );
        Services::Registry()->set('Triggerdata', 'GridTableColumns', $grid_columns);

        $connect->model->query->where(
            $connect->model->db->qn($primary_prefix)
                . '.' . $connect->model->db->qn('catalog_type_id')
                . ' = ' . $this->get('menuitem_source_catalog_type_id')
        );
        $connect->model->query->where(
            $connect->model->db->qn('catalog.redirect_to_id') . ' = ' . 0);

        $ordering = $this->get('grid_ordering', 'start_publishing_datetime');
        Services::Registry()->set('Triggerdata', 'GridTableOrdering', $ordering);
        $connect->model->query->order($connect->model->db->qn($ordering));

        $connect->set('model_offset', 0);
        $connect->set('model_offset', 0);
        $connect->set('model_count', 10);

        $query_results = $connect->getData('list');

        Services::Registry()->set('Parameters', 'model_name', 'Triggerdata');
        Services::Registry()->set('Parameters', 'model_type', 'dbo');
        Services::Registry()->set('Parameters', 'model_query_object', 'getTriggerdata');
        Services::Registry()->set('Parameters', 'model_parameter', 'GridQueryResults');

        Services::Registry()->set('Triggerdata', 'GridQueryResults', $query_results);

        return true;
    }

    /**
     * Create Pagination data and store in Trigger registry
     *
     * @return boolean
     * @since  1.0
     */
    protected function setPagination()
    {
        $url = Services::Registry()->get('Parameters', 'full_page_url');
        $connector = '';
        $query_results = array();
        $current = 0;

        $row = new \stdClass();
        $row->link = $url . $connector . '&start=' . $current + 5;
        $row->class = ' page-prev';
        $row->link_text = ' 1';

        $query_results[] = $row;

        $row = new \stdClass();
        $row->link = $url . $connector . '&start=' . $current + 10;
        $row->class = '';
        $row->link_text = ' 2';

        $query_results[] = $row;

        $row = new \stdClass();
        $row->link = $url . $connector . '&start=' . $current + 15;
        $row->class = ' page-next';
        $row->link_text = ' 3';

        $query_results[] = $row;

        Services::Registry()->set('Triggerdata', 'GridPagination', $query_results);
        Services::Registry()->set('Triggerdata', 'GridPaginationOffset', $this->get('grid_offset', 0));
        Services::Registry()->set('Triggerdata', 'GridPaginationCount', $this->get('grid_count', 5));

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

        /** Add lists needed for Batch Updates */
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

                if ($items == false) {
                } else {

                    if ($listname == 'Status') {
                        $multiple = 0;
                        $size = 0;
                    } else {
                        $multiple = 1;
                        $size = 5;
                    }
                    $query_results = Services::Text()->buildSelectlist($listname, $items, $multiple, $size);

                    Services::Registry()->set('Triggerdata', 'listbatch_' . $listname, $query_results);

                    $row = new \stdClass();
                    $row->listname = $listname;
                    $names_of_lists[] = $row;
                }
            }
        }

        Services::Registry()->set('Triggerdata', 'GridBatchFilters', $names_of_lists);

        return true;
    }
}
