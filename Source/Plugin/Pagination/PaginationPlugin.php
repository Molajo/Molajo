<?php
/**
 * Pagination Plugin
 *
 * @package    Molajo
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagination;

use stdClass;
use CommonApi\Event\DisplayInterface;
use Molajo\Plugin\DisplayEventPlugin;

/**
 * Pagination Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class PaginationPlugin extends DisplayEventPlugin implements DisplayInterface
{
    /**
     * After reading, calculate pagination data
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeParse()
    {
        if (isset($this->runtime_data->render->token)
            && $this->runtime_data->render->token->type == 'template'
            && $this->runtime_data->render->token->name == 'pagination'
        ) {
        } else {
            return $this;
        }

        $use_pagination = $this->runtime_data->resource->parameters->use_pagination;
        if ((int)$use_pagination > 0) {
        } else {
            return $this;
        }

        /** initialise */
        $url                = $this->runtime_data->page->urls['page'];
        $temp_query_results = array();

        /** pagination_total: number of items */
        $pagination_total = $this->runtime_data->resource->parameters->pagination_total;
        if ((int)$pagination_total > 1) {
        } else {
            return $this;
        }

        /** model_count: max number of rows to display per page */
        $model_count = $this->runtime_data->resource->parameters->model_count;
        if ((int)$model_count > 0) {
        } else {
            $model_count = 15;
        }

        /** model_offset: offset of 0 means skip 0 rows, then start with row 1 */
        $model_offset = $this->runtime_data->resource->parameters->model_offset;
        if ((int)$model_offset > 0) {
        } else {
            $model_offset = 0;
        }

        /** current_page */
        $current_page = ($model_offset / $model_count) + 1;

        if ($model_offset % $model_count) {
            $current_page ++;
        }

        /** previous page */
        if ((int)$current_page > 1) {
            $previous_page = (int)$current_page - 1;
            $prev_link     = $url . '/page/' . (int)$previous_page;
        } else {
            $previous_page = 0;
            $prev_link     = '';
        }

        /** total pages */
        $total_pages = $pagination_total / $model_count;

        if ($pagination_total % $model_count) {
            $total_pages ++;
        }

        /** next page */
        if ((int)$total_pages > (int)$current_page) {
            $next_page = $current_page + 1;
            $next_link = $url . '/page/' . $next_page;
        } else {
            $next_page = 0;
            $next_link = '';
        }

        /** first and last pages */
        $first_page = 1;
        $first_link = $url . '/page/' . 1;

        $last_page = (int)$total_pages;
        $last_link = $url . '/page/' . (int)$total_pages;

        /** Paging */
        $temp_row = new stdClass();

        $temp_row->total_items          = (int)$pagination_total;
        $temp_row->total_items_per_page = (int)$model_count;

        $temp_row->first_page = $first_page;
        $temp_row->first_link = $first_link;

        $temp_row->previous_page = $previous_page;
        $temp_row->prev_link     = $prev_link;

        $temp_row->next_page = $next_page;
        $temp_row->next_link = $next_link;

        $temp_row->last_page = $last_page;
        $temp_row->last_link = $last_link;

        $temp_query_results[] = $temp_row;

        $this->runtime_data->plugin_data->paging = $temp_query_results;

        /** Paging */
        $temp_query_results = array();
        if ($total_pages > 10) {
            $total_pages = 10;
        }

        $grid_list = array();

        for ($i = 1; $i < $total_pages; $i ++) {

            $temp_row = new stdClass();

            $temp_row->total_items          = $pagination_total;
            $temp_row->total_items_per_page = $model_count;

            $temp_row->first_page = $first_page;
            $temp_row->first_link = $first_link;

            $temp_row->previous_page = $previous_page;
            $temp_row->prev_link     = $prev_link;

            if ($i == $current_page) {
                $temp_row->current = 1;
            } else {
                $temp_row->current = 0;
            }

            $temp_row->link      = $url . '/page/' . $i;
            $temp_row->link_text = ' ' . (int)$i;

            $temp_row->next_page = $next_page;
            $temp_row->next_link = $next_link;

            $temp_row->last_page = $last_page;
            $temp_row->last_link = $last_link;

            $temp_query_results[] = $temp_row;
        }

        $this->runtime_data->plugin_data->pagination = $temp_query_results;

        return $this;
    }

    /**
     * Prev and Next Pagination for Item Pages
     *
     * @return bool
     * @since  1.0
     */
    protected function itemPaging()
    {
        $controller_class_namespace = $this->controller_namespace;
        $controller                 = new $controller_class_namespace();
        $controller->getModelRegistry(
            $this->get('model_type', 'datasource'),
            $this->get('model_name', '', 'runtime_data'),
            1
        );

        $controller->set('get_customfields', 0);
        $controller->set('use_special_joins', 0);
        $controller->set('process_events', 0);
        $controller->set('get_item_children', 0);

        $controller->model->query->select(
            $controller->model->database->qn('a')
            . '.' . $controller->model->database->qn($controller->get('primary_key', 'id'))
        );

        $controller->model->query->select(
            $controller->model->database->qn('a')
            . '.' . $controller->model->database->qn($controller->get('name_key', 'title'))
        );

        $controller->model->query->where(
            $controller->model->database->qn('a')
            . '.' . $controller->model->database->qn(
                $controller->get('primary_key', 'id')
                . ' = ' . (int)$this->runtime_data->catalog->source_id
            )
        );

//@todo ordering
        $item = $controller->getData('item');

        $this->model_registry_name = ucfirst(strtolower($this->get('model_name', '', 'runtime_data')))
            . ucfirst(strtolower($this->get('model_type', 'datasource')));

        if ($item === false || count($item) == 0) {
            return $this;
        }
    }
}
