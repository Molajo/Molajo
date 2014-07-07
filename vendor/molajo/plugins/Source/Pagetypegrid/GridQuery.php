<?php
/**
 * Page Type Grid Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypegrid;

use CommonApi\Event\DisplayInterface;
use stdClass;

/**
 * Page Type Grid Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
class GridQuery extends Base implements DisplayInterface
{
    /**
     * Ordering
     *
     * @var    string
     * @since  1.0.0
     */
    protected $ordering;

    /**
     * Direction
     *
     * @var    string
     * @since  1.0.0
     */
    protected $direction;

    /**
     * Items Per Page
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $items_per_page;

    /**
     * Offset
     *
     * @var    integer
     * @since  1.0.0
     */
    protected $offset;

    /**
     * Grid Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridData()
    {
        $controller = $this->getGridDataQuery();
        $results    = $this->runQuery($controller);

        $name_key = $controller->getModelRegistry('name_key');

        $grid_items = array();

        if (count($results) > 0) {
            foreach ($results as $item) {
                $grid_items = $this->setGridDataItem($item, $name_key, $grid_items);
            }
        }

        $this->plugin_data->grid_data           = $grid_items;
        $this->plugin_data->grid_model_registry = $controller->getModelRegistry('*');

        $this->getGridOrderingTemplate();

        return $this;
    }

    /**
     * Grid Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function setGridDataItem($item, $name_key, $grid_items)
    {
        $name     = $item->$name_key;
        $temp_row = clone $item;

        if (isset($item->lvl)) {
        } else {
            $grid_items[] = $item;
            return $grid_items;
        }

        $lvl = (int)$item->lvl - 1;

        $name = $this->setLevelDots($lvl, $name);

        $temp_row->$name_key = $name;

        $temp_row->home_url     = $this->plugin_data->page->urls['home'];
        $temp_row->page_url     = $this->plugin_data->page->urls['home'];
        $temp_row->username_url = '';

        $grid_items[] = $temp_row;

        return $grid_items;
    }

    /**
     * Grid Ordering Template
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridOrderingTemplate()
    {
        $temp                 = new stdClass();
        $temp->ordering       = $this->ordering;
        $temp->direction      = $this->direction;
        $temp->items_per_page = $this->items_per_page;
        $temp->offset         = $this->offset;

        $this->plugin_data->grid_ordering = $temp;

        return $this;
    }

    /**
     * Grid Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQuery()
    {
        $parameters = $this->runtime_data->resource->menuitem->parameters;
        $resource   = $this->runtime_data->resource->resource_model_name;

        $model = 'Molajo//' . $resource . '//Configuration.xml';

        $controller = $this->resource->get('query:///' . $model);

        $this->getGridDataQueryRegistry($controller);
        $this->getGridDataQueryWhere($controller);

        if ((int)$parameters->grid_pagination_use === 1) {
            $this->getGridDataQueryPagination($controller, $parameters);
        } else {
            $controller->setModelRegistry('model_use_pagination', 0);
        }

        return $this;
    }

    /**
     * Grid Query Registry
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryRegistry($controller)
    {
        $controller->setModelRegistry('check_view_level_access', 1);
        $controller->setModelRegistry('process_events', 1);
        $controller->setModelRegistry('query_object', 'list');
        $controller->setModelRegistry('get_customfields', 1);
        $controller->setModelRegistry('use_special_joins', 1);

        return $this;
    }

    /**
     * Grid Query
     *
     * @param   object $controller
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryWhere($controller)
    {
        $parameters      = $this->runtime_data->resource->menuitem->parameters;
        $catalog_type_id = $parameters->criteria_catalog_type_id;
        $prefix          = $controller->getModelRegistry('primary_prefix');

        $controller->where('column', $prefix . '.' . 'catalog_type_id', '=', 'integer', (int)$catalog_type_id);

        $list = $parameters->grid_status;
        if ($list === '') {
        } else {
            $controller->where('column', $prefix . '.' . 'status', 'IN', 'integer', $list);
        }

        $controller->where('column', 'catalog.redirect_to_id', '=', 'integer', 0);

        return $this;
    }

    /**
     * Grid Query Pagination
     *
     * @param   GridQuery $controller
     * @param   array  $parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryPagination($controller, $parameters)
    {
        $prefix = $controller->getModelRegistry('primary_prefix');

        $this->getGridDataQueryPaginationOrdering($parameters);
        $this->getGridDataQueryPaginationDirection($parameters);

        $controller->orderBy($prefix . '.' . $this->ordering, $this->direction);

        $this->getGridDataQueryPaginationOffset($parameters);
        $controller->setModelRegistry('model_offset', $this->offset);

        $this->getGridDataQueryPaginationItemsPerPage($parameters);

        $controller->setModelRegistry('model_count', $this->items_per_page);
        $controller->setModelRegistry('model_use_pagination', 1);

        return $this;
    }

    /**
     * Grid Query Pagination
     *
     * @param   array  $parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryPaginationOrdering($parameters)
    {
        $this->ordering = $parameters->grid_pagination_ordering_column;
        if ($this->ordering === '' || $this->ordering === null) {
            $this->ordering = $this->runtime_data->resource->model_registry['primary_key'];
        }

        return $this;
    }

    /**
     * Grid Query Pagination Direction
     *
     * @param   array $parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryPaginationDirection($parameters)
    {
        $this->direction = $parameters->grid_pagination_ordering_direction;
        if ($this->direction === 'DESC') {
        } else {
            $this->direction = 'ASC';
        }

        return $this;
    }

    /**
     * Grid Query Offset
     *
     * @return  GridQuery
     * @since   1.0.0
     */
    protected function getGridDataQueryPaginationOffset($parameters)
    {
        if ((int)$parameters->offset === 0) {
            $this->offset = 0;
        }

        return $this;
    }

    /**
     * Grid Query Pagination Items Per Page
     *
     * @param   array $parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryPaginationItemsPerPage($parameters)
    {
        $this->items_per_page = (int)$parameters->grid_pagination_items_per_page;
        if ((int)$this->items_per_page === 0) {
            $this->items_per_page = 10;
        }

        return $this;
    }
}
