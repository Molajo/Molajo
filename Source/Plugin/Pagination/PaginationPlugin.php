<?php
/**
 * Pagination Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugin\Pagination;

use CommonApi\Event\ReadInterface;
use Molajo\Plugin\ReadEventPlugin;
use Molajo\Render\Pagination as Pagination;

/**
 * Pagination Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class PaginationPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * After reading, calculate pagination data
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterReadall()
    {
        if (isset($this->model_registry['use_pagination'])
            && isset($this->model_registry['total_items'])
            && isset($this->model_registry['model_count'])
            && isset($this->runtime_data->request->data->url)
            && isset($this->query_results)
        ) {
        } else {
            return $this;
        }

        if ((int)$this->model_registry['use_pagination'] === 0) {
            return $this;
        }

        /** From Http Request Class */
        $page_url = $this->runtime_data->request->data->url;
//        $query_parameters = array('tag' => 'celebrate'); // Exclude the page parameter
        $query_parameters = array(); // Exclude the page parameter
        $page = 1;

        /** From Database Query */
        $data = $this->query_results;

        $total_items = $this->model_registry['total_items'];

        /** Application Configuration */
        $per_page = $this->model_registry['model_count'];          // How many items should display on the page?
        $display_links = 5;     // How many numeric page links should display in the pagination?

        /** Instantiate the Pagination Adapter */
        $pagination = new Pagination(
            $data,
            $page_url,
            $query_parameters,
            $total_items,
            $per_page,
            $display_links,
            $page
        );

        $this->runtime_data->plugin_data->pagination = $pagination;

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
