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

        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry($this->get('model_type'), $this->get('model_name'));
        if ($results === false) {
            return false;
        }

        $results = $controller->setDataobject();
        if ($results === false) {
            return false;
        }

        $controller->set('get_customfields', 2);
        $controller->set('use_special_joins', 1);
        $controller->set('process_plugins', 1);

        $this->setToolbar();

        $this->setFilter();

        $this->setGrid($controller);

        $this->setBatch();

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
     * @return  boolean
     * @since   1.0
     */
    protected function setFilter()
    {
        $grid_list = array();
        for ($i=1; $i < 11; $i++) {
            if ($this->get('grid_list' . $i, '') == '') {
            } else {
                $grid_list[] = $this->get('grid_list'. $i);
            }
        }

        $lists = array();
        if (is_array($grid_list) && count($grid_list) > 0) {

            foreach ($grid_list as $listname) {

                //todo: figure out selected value
                $selected = '';
                $results = Services::Text()->getList($listname, $this->parameters);

                if ($results === false) {
                } else {

                    $query_results = Services::Text()->buildSelectlist(
                        $listname,
                        $results[0]->listitems,
                        $results[0]->multiple,
                        $results[0]->size,
                        $selected
                    );

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
     * @param   $controller
     *
     * @return  bool
     * @since   1.0
     */
    protected function setGrid($controller)
    {
        $grid_columns = array();
        for ($i=1; $i < 16; $i++) {
            $item = $this->get('grid_column' . $i);
            if (trim($item) == '') {
            } else {
                $grid_columns[] = trim($item);
            }
        }

        Services::Registry()->set('Plugindata', 'GridTableColumns', $grid_columns);

        $list = $this->get('criteria_catalog_type_id');

        $controller->model->query->where(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn('catalog_type_id')
                . ' IN (' . $list . ')'
        );

        $controller->model->query->where($controller->model->db->qn('catalog.redirect_to_id') . ' = ' . 0);

        $ordering = $this->get('grid_ordering');
        if ($ordering == '' || $ordering === null) {
            $ordering = $controller->get('primary_key', 'id');
        }
        Services::Registry()->set('Plugindata', 'GridTableOrdering', $ordering);

        $orderingDirection = $this->get('grid_ordering_direction');
        if ($orderingDirection == 'ASC') {
        } else {
            $orderingDirection = 'DESC';
        }
        Services::Registry()->set('Plugindata', 'GridTableOrderingDirection', $orderingDirection);

        $controller->model->query->order(
            $controller->model->db->qn($controller->get('primary_prefix'))
                . '.' . $controller->model->db->qn($ordering)
                . ' '
                . $orderingDirection
        );

        $offset = (int) $this->get('grid_offset');
        Services::Registry()->set('Plugindata', 'GridTableOffset', (int) $offset);
        $controller->set('model_offset', $offset);

        $itemsPerPage = (int) $this->get('grid_items_per_page');
        if ((int)$itemsPerPage == 0) {
            $itemsPerPage = 15;
        }
        Services::Registry()->set('Plugindata', 'GridTableItemsPerPage', $itemsPerPage);

        $controller->set('model_count', $itemsPerPage);

        /** Run Query */
        $query_results = $controller->getData('list');

        $gridItems = array();

        $name_key = $controller->get('name_key');

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
        echo $controller->model->query->__toString();
        echo '<br /><br />';
*/

        $this->set('model_type', 'Plugindata');
        $this->set('model_name', 'PrimaryRequestQueryResults');
        $this->set('model_query_object', 'list');

        $this->parameters['model_type'] = 'Plugindata';
        $this->parameters['model_name'] = 'PrimaryRequestQueryResults';

        Services::Registry()->set('Plugindata', 'PrimaryRequestQueryResults', $query_results);

        return true;
    }

    /**
     * Creates and stores lists for Grid Batch area
     *
     * @param   $controller
     * @param   $primary_prefix
     *
     * @return boolean
     * @since   1.0
     */
    protected function setBatch()
    {
        $temp = $this->get('grid_batch_array', '');

        if ($temp == '') {
            Services::Registry()->set('Plugindata', 'SectionSubmenu', array());
            return true;
        }

        $grid_batch_array = explode(',', $temp);
        if (count($grid_batch_array) == 0) {
            Services::Registry()->set('Plugindata', 'SectionSubmenu', array());
            return true;
        }

        $grid_batch = array();
        for ($i=0; $i < count($grid_batch_array); $i++) {

            $enable = (int) $this->get('grid_batch_' . strtolower($grid_batch_array[$i]));

            if ((int) $enable == 0) {
            } else {

                $grid_batch[] = strtolower($grid_batch_array[$i]);

                $row = new \stdClass();
                $row->selected = '';
                $row->enable = 1;

                Services::Registry()->set(
                    'Plugindata',
                    'Grid' . strtolower($grid_batch_array[$i]),
                    array($row)
                );
            }
        }

        /** Submenu: Links to various Form Pages (Tabs) - ex. Batch Options */
        $pageArray = array();
        $i = 0;
        foreach ($grid_batch as $item) {
            if ($item == '') {
            } else {
                $row = new \stdClass();

                $row->id = strtolower($item);
                if ($i == 0) {
                    $row->current = 1;
                } else {
                    $row->current = 0;
                }
                $row->title = ucfirst(strtolower($item));
                $row->url = Services::Registry()->get('Plugindata', 'page_url') . '#lk' . strtolower($item);

                $pageArray[] = $row;

                $i++;
            }
        }
        Services::Registry()->set('Plugindata', 'SectionSubmenu', $pageArray);

        return true;
    }
}
