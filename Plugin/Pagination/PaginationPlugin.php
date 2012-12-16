<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
namespace Molajo\Plugin\Pagination;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Pagination
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class PaginationPlugin extends Plugin
{
    /**
     * After reading, calculate pagination data
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterReadall()
    {

        if (strtolower($this->get('template_view_path_node', '', 'parameters')) == 'pagination') {
            return true;
        }

        if ((int) $this->get('use_pagination', 0, 'parameters') > 0) {
        } else {
            return true;
        }

        /** initialise */
        $url = Services::Registry()->get(PAGE_LITERAL, 'page_url');
        $temp_query_results = array();

        /** pagination_total: number of items */
        if ((int) $this->get('pagination_total') > 1) {
        } else {
            return true;
        }

        /** model_count: max number of rows to display per page */
        if ((int) $this->get('model_count', 0, 'parameters') > 0) {
        } else {
            $this->set('model_count', 10);
        }

        /** model_offset: offset of 0 means skip 0 rows, then start with row 1 */
        if ((int) $this->get('model_offset', 0, 'parameters') > 1) {
        } else {
            $this->set('model_offset', 0, 'parameters');
        }

        /** current_page */
        $current_page = ($this->get('model_offset', 0, 'parameters')
            / $this->get('model_count', 0, 'parameters')) + 1;

        if ($this->get('model_offset', 0, 'parameters')
            % $this->get('model_count', 0, 'parameters')) {
            $current_page++;
        }

        /** previous page */
        if ((int) $current_page > 1) {
            $previous_page = (int) $current_page - 1;
            $prev_link = $url . '/page/' . (int) $previous_page;
        } else {
            $previous_page = 0;
            $prev_link = '';
        }

        /** total pages */
        $total_pages = (int) $this->get('pagination_total', 0, 'parameters')
            / (int) $this->get('model_count', 0, 'parameters');

        if ((int) $this->get('pagination_total', 0, 'parameters')
            % $this->get('model_count', 0, 'parameters') > 0) {
            $total_pages++;
        }

        /** next page */
        if ((int) $total_pages > (int) $current_page) {
            $next_page = $current_page + 1;
            $next_link = $url . '/page/' . $next_page;
        } else {
            $next_page = 0;
            $next_link = '';
        }

        /** first and last pages */
        $first_page = 1;
        $first_link = $url . '/page/' . 1;

        $last_page = (int) $total_pages;
        $last_link = $url . '/page/' . (int) $total_pages;

        /** Paging */
        $temp_row = new \stdClass();

        $temp_row->total_items = (int) $this->get('pagination_total', 0, 'parameters');
        $temp_row->total_items_per_page = (int) $this->get('model_count', 0, 'parameters');

        $temp_row->first_page = $first_page;
        $temp_row->first_link = $first_link;

        $temp_row->previous_page = $previous_page;
        $temp_row->prev_link = $prev_link;

        $temp_row->next_page = $next_page;
        $temp_row->next_link = $next_link;

        $temp_row->last_page = $last_page;
        $temp_row->last_link = $last_link;

        $temp_query_results[] = $temp_row;

        Services::Registry()->set(PRIMARY_LITERAL, 'Paging', $temp_query_results);

        /** Paging */
        $temp_query_results = array();
        if ($total_pages > 10) {
            $total_pages = 10;
        }
        for ($i = 1; $i < $total_pages; $i++) {

            $temp_row = new \stdClass();

            $temp_row->total_items = (int) $this->get('pagination_total', 0, 'parameters');
            $temp_row->total_items_per_page = (int) $this->get('model_count', 0, 'parameters');

            $temp_row->first_page = $first_page;
            $temp_row->first_link = $first_link;

            $temp_row->previous_page = $previous_page;
            $temp_row->prev_link = $prev_link;

            if ($i == $current_page) {
                $temp_row->current = 1;
            } else {
                $temp_row->current = 0;
            }

            $temp_row->link = $url . '/page/' . $i;
            $temp_row->link_text = ' ' . (int) $i;

            $temp_row->next_page = $next_page;
            $temp_row->next_link = $next_link;

            $temp_row->last_page = $last_page;
            $temp_row->last_link = $last_link;

            $temp_query_results[] = $temp_row;
        }

        Services::Registry()->set(TEMPLATE_LITERAL, 'Pagination', $temp_query_results);

        return true;
    }

    /**
     * Prev and Next Pagination for Item Pages
     *
     * @return bool
     * @since  1.0
     */
    protected function itemPaging()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();
        $controller->getModelRegistry(
            $this->get('model_type', DATA_SOURCE_LITERAL),
            $this->get('model_name', '', 'parameters'),
            1
        );

        $controller->set('get_customfields', 0, 'model_registry');
        $controller->set('use_special_joins', 0, 'model_registry');
        $controller->set('process_plugins', 0, 'model_registry');
        $controller->set('get_item_children', 0, 'model_registry');

        $controller->model->query->select($controller->model->db->qn('a')
            . '.' . $controller->model->db->qn($controller->get('primary_key', 'id', 'model_registry')));

        $controller->model->query->select($controller->model->db->qn('a')
            . '.' . $controller->model->db->qn($controller->get('name_key', 'title')));

        $controller->model->query->where($controller->model->db->qn('a')
            . '.' . $controller->model->db->qn($controller->get('primary_key', 'id', 'model_registry')
            . ' = ' . (int) $this->parameters['catalog_source_id']));

//@todo ordering
        $item = $controller->getData(QUERY_OBJECT_ITEM);

        $this->model_registry_name = ucfirst(strtolower($this->get('model_name', '', 'parameters')))
            . ucfirst(strtolower($this->get('model_type', DATA_SOURCE_LITERAL)));

        if ($item === false || count($item) == 0) {
            return false;
        }
    }
}
