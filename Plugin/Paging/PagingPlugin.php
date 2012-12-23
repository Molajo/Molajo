<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
namespace Molajo\Plugin\Paging;

use Molajo\Service\Services;
use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Paging
 *
 * @package     Niambie
 * @license     MIT
 * @since       1.0
 */
class PagingPlugin extends Plugin
{
    /**
     * After reading, calculate paging data
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeInclude()
    {
        return;
        if (strtolower($this->get('template_view_path_node', '', 'parameters')) == 'paging') {
        } else {
            return true;
        }

        /** initialise */
        $url = Services::Registry()->get(PAGE_LITERAL, 'page_url');


        /** current_page */
        $current_page = ($this->get('model_offset') / $this->get('model_count', 0, 'parameters')) + 1;
        if ($this->get('model_offset') % $this->get('model_count', 0, 'parameters')) {
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

        /** next page */
        if ((int) $total_pages > (int) $current_page) {
            $next_page = $current_page + 1;
            $next_link = $url . '/page/' . $next_page;
        } else {
            $next_page = 0;
            $next_link = '';
        }

        /** Paging */
        $temp_row = new \stdClass();

        $temp_row->total_items = (int) $this->get('pagination_total');
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
    }
    /**
     * Prev and Next Paging for Item Pages
     *
     * @return bool
     */
    protected function itemPaging()
    {
        $controllerClass = CONTROLLER_CLASS;
        $controller = new $controllerClass();

        $results = $controller->getModelRegistry(
            $this->get('model_type', DATA_SOURCE_LITERAL),
            $this->get('model_name', '', 'parameters'),
            1
        );

        $controller->setDataobject();
        $controller->connectDatabase();

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
