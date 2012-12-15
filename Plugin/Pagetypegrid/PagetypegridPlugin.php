<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Pagetypegrid;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('NIAMBIE') or die;

/**
 * @package     Niambie
 * @license     GNU GPL v 2, or later and MIT
 * @since       1.0
 */
class PagetypegridPlugin extends Plugin
{
    /**
     * Prepares data for the Administrator Grid
     *
     * @return  boolean
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (strtolower($this->get('page_type', '', 'parameters')) == strtolower(PAGE_TYPE_GRID)) {
        } else {
            return true;
        }

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(
            $this->get('model_type', '', 'parameters'),
            $this->get('model_name', '', 'parameters'),
            1
        );
        $controller->set('get_customfields', 2, 'model_registry');
        $controller->set('use_special_joins', 1, 'model_registry');
        $controller->set('process_plugins', 1, 'model_registry');

        $this->setToolbar();

        $this->setFilter();

        $this->setGrid($controller);

        $this->setBatch();

        return true;
    }

    /**
     * Create Toolbar Registry based on Authorized Access
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setToolbar()
    {
        $url = Services::Registry()->get(PAGE_LITERAL, 'page_url');

        $list = $this->get('grid_toolbar_buttons', '', 'parameters');

        if ($list == '#' || $list == '') {
            $list = 'create,read,edit,publish,feature,archive,checkin,restore,delete,trash';
        }

        $grid_toolbar_buttons = explode(',', $list);

        $permissions = Services::Permissions()
            ->verifyTaskList(
            $grid_toolbar_buttons,
            $this->get('catalog_id', '', 'permissions')
        );

        $temp_query_results = array();

        foreach ($grid_toolbar_buttons as $button) {

            if ($permissions[$button] === true) {
                $temp_row = new \stdClass();

                $temp_row->name = Services::Language()->translate(strtoupper('TASK_' . strtoupper($button) . '_BUTTON'));
                $temp_row->action = $button;

                if (Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef', 1) == 1) {
                    $temp_row->link = $url . '/' . $temp_row->action;
                } else {
                    $temp_row->link = $url . '&action=' . $temp_row->action;
                }

                $temp_query_results[] = $temp_row;
            }
        }

        if ($this->get('grid_search', 1) == 1) {
            $temp_row = new \stdClass();

            $temp_row->name = Services::Language()->translate(strtoupper('TASK_' . 'SEARCH' . '_BUTTON'));
            $temp_row->action = 'search';

            if (Services::Registry()->get(CONFIGURATION_LITERAL, 'url_sef', 1) == 1) {
                $temp_row->link = $url . '/' . $temp_row->action;
            } else {
                $temp_row->link = $url . '&action=' . $temp_row->action;
            }

            $temp_query_results[] = $temp_row;
        }

        Services::Registry()->set(TEMPLATE_LITERAL, 'Toolbar', $temp_query_results);

        return true;
    }

    /**
     * Filters: lists stored in registry, where clauses for primary grid query set
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setFilter()
    {
        $grid_list = array();

        for ($i = 1; $i < 11; $i++) {
            if ($this->get('grid_list' . $i, '', 'parameters') == '') {
            } else {
                $grid_list[] = $this->get('grid_list' . $i, '', 'parameters');
            }
        }

        $lists = array();
        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $listname) {

                //@todo figure out selected value
                $selected = '';

                $results = Services::Text()->getDatalist($listname, DATALIST_LITERAL, $this->parameters);

                if ($results === false) {
                } else {

                    $temp_query_results = Services::Text()->buildSelectlist(
                        $listname,
                        $results[0]->listitems,
                        $results[0]->multiple,
                        $results[0]->size,
                        $selected
                    );

                    Services::Registry()->set(DATALIST_LITERAL, $listname, $temp_query_results);

                    $temp_row = new \stdClass();
                    $temp_row->listname = $listname;
                    $lists[] = $temp_row;
                }
            }
        }

        Services::Registry()->set(TEMPLATE_LITERAL, 'Gridfilters', $lists);

        return true;
    }

    /**
     * Grid Query: results stored in Plugin registry
     *
     * @param   $controller
     *
     * @return  bool
     * @since   1.0
     */
    protected function setGrid($controller)
    {
        $grid_columns = array();

        for ($i = 1; $i < 16; $i++) {
            $item = $this->get('grid_column' . $i);
            if (trim($item) == '') {
            } else {
                $grid_columns[] = trim($item);
            }
        }

        Services::Registry()->set(PAGE_TYPE_GRID, 'TableColumns', $grid_columns);

        $list = $this->get('criteria_catalog_type_id', '', 'parameters');

        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn('catalog_type_id')
                . ' IN (' . $list . ')'
        );

        $controller->model->query->where($controller->model->db->qn('catalog.redirect_to_id') . ' = ' . 0);

        $ordering = $this->get('grid_ordering');
        if ($ordering == '' || $ordering === null) {
            $ordering = $controller->get('primary_key', 'id', 'model_registry');
        }
        Services::Registry()->set(PAGE_TYPE_GRID, 'Tableordering', $ordering);

        $orderingDirection = $this->get('grid_ordering_direction');
        if ($orderingDirection == 'ASC') {
        } else {
            $orderingDirection = 'DESC';
        }
        Services::Registry()->set(PAGE_TYPE_GRID, 'Orderingdirection', $orderingDirection);

        $controller->model->query->order(
            $controller->model->db->qn($controller->get('primary_prefix', 'a', 'model_registry'))
                . '.' . $controller->model->db->qn($ordering)
                . ' '
                . $orderingDirection
        );

        $offset = (int)$this->get('grid_offset', '', 'parameters');
        Services::Registry()->set(PAGE_TYPE_GRID, 'Offset', (int)$offset);
        $controller->set('model_offset', $offset, 'parameters');

        $itemsPerPage = (int)$this->get('grid_items_per_page', '', 'parameters');
        if ((int)$itemsPerPage == 0) {
            $itemsPerPage = 15;
        }
        Services::Registry()->set(PAGE_TYPE_GRID, 'ItemsPerPage', $itemsPerPage);

        $controller->set('model_count', $itemsPerPage, 'model_registry');

        $temp_query_results = $controller->getData(QUERY_OBJECT_LIST);

        $gridItems = array();

        $name_key = $controller->get('name_key', '', 'parameters');

        if (count($temp_query_results) > 0) {

            foreach ($temp_query_results as $item) {
                $temp_row = new \stdClass();
                $temp_row = $item;
                $name = $item->$name_key;

                if (isset($item->lvl)) {
                } else {
                    $gridItems = $temp_query_results;
                    break;
                }
                $lvl = (int)$item->lvl - 1;

                if ($lvl > 0) {
                    for ($i = 0; $i < $lvl; $i++) {
                        $name = ' ..' . $name;
                    }
                }
                $temp_row->$name_key = $name;

                $gridItems[] = $temp_row;
            }
        }

        $controller->set('request_model_type',
            $this->get('model_type', '', 'parameters'),
            'model_registry');
        $controller->set('request_model_name',
            $this->get('model_name', '', 'parameters'),
            'model_registry');

        $controller->set('model_type', DATA_OBJECT_LITERAL, 'model_registry');
        $controller->set('model_name', PRIMARY_LITERAL, 'model_registry');
        $controller->set('model_query_object', QUERY_OBJECT_LIST, 'model_registry');

        Services::Registry()->set(PRIMARY_LITERAL, DATA_LITERAL, $temp_query_results);

        return true;
    }

    /**
     * Creates and stores lists for Grid Batch area
     *
     * @param   $controller
     * @param   $primary_prefix
     *
     * @return  boolean
     * @since   1.0
     */
    protected function setBatch()
    {
        $temp = $this->get('grid_batch_array', '', 'parameters');

        if ($temp == '') {
            Services::Registry()->set(PAGE_LITERAL, 'SectionSubmenu', array());
            return true;
        }

        $grid_batch_array = explode(',', $temp);
        if (count($grid_batch_array) == 0) {
            Services::Registry()->set(PAGE_LITERAL, 'SectionSubmenu', array());
            return true;
        }

        $grid_batch = array();
        for ($i = 0; $i < count($grid_batch_array); $i++) {

            $enable = (int)$this->get('grid_batch_' . strtolower($grid_batch_array[$i]));

            if ((int)$enable == 0) {
            } else {

                $grid_batch[] = strtolower($grid_batch_array[$i]);

                $temp_row = new \stdClass();
                $temp_row->selected = '';
                $temp_row->enable = 1;

                Services::Registry()->set(TEMPLATE_LITERAL, 'Grid' . strtolower($grid_batch_array[$i]), array($temp_row));
            }
        }

        $pageArray = array();
        $i = 0;

        foreach ($grid_batch as $item) {
            if ($item == '') {
            } else {
                $temp_row = new \stdClass();

                $temp_row->id = strtolower($item);
                if ($i == 0) {
                    $temp_row->current = 1;
                } else {
                    $temp_row->current = 0;
                }
                $temp_row->title = ucfirst(strtolower($item));
                $temp_row->url = Services::Registry()->get(PAGE_LITERAL, 'page_url') . '#lk' . strtolower($item);

                $pageArray[] = $temp_row;

                $i++;
            }
        }

        Services::Registry()->set(PAGE_LITERAL, 'SectionSubmenu', $pageArray);

        return true;
    }
}
