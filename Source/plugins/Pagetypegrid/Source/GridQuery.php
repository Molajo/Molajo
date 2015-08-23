<?php
/**
 * Page Type Grid Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Pagetypegrid;

use CommonApi\Event\DisplayEventInterface;
use stdClass;

/**
 * Page Type Grid Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
abstract class GridQuery extends Base implements DisplayEventInterface
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
        $this->getGridDataQuery();
        $results  = $this->runQuery();
        $name_key = $this->query->getModelRegistry('name_key');

        $grid_items = array();

        if (count($results) > 0) {
            foreach ($results as $item) {
                $grid_items = $this->setGridDataItem($item, $name_key, $grid_items);
            }
        }

        $this->plugin_data->grid_data           = $grid_items;
        $this->plugin_data->grid_model_registry = $this->query->getModelRegistry();

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

        $temp_row->home_url     = $this->runtime_data->application->base_url;
        $temp_row->page_url     = $this->runtime_data->application->base_url;
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
        $parameters = $this->runtime_data->resource->parameters;

        $model_name = ucfirst(strtolower(trim($parameters->model_name)));
        $model_type = ucfirst(strtolower(trim($parameters->model_type)));

        $model = 'Molajo//' . $model_type . '//' . $model_name . '//Content.xml';

        $this->setQueryController($model);

        $this->setQueryControllerDefaults(
            $process_events = 1,
            $query_object = 'list',
            $get_customfields = 1,
            $use_special_joins = 1,
            $use_pagination = 0,
            $check_view_level_access = 1,
            $get_item_children = 0
        );

        $this->getGridDataQueryWhere();

        if ((int)$parameters->grid_pagination_use === 1) {
            $this->getGridDataQueryPagination($parameters);
        } else {
            $this->query->setModelRegistry('model_use_pagination', 0);
        }

        return $this;
    }

    /**
     * Grid Query
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryWhere()
    {
        $parameters = $this->runtime_data->resource->parameters;
        $prefix     = $this->query->getModelRegistry('primary_prefix');

        $catalog_type_id = $parameters->criteria_catalog_type_id;
        if ((int)$catalog_type_id === 0) {
        } elseif (isset($this->model_registry['fields']['catalog_type_id'])) {
            $this->query->where('column', $prefix . '.' . 'catalog_type_id', '=', 'integer', $catalog_type_id);
        }

        $list = $parameters->criteria_status;
        if (trim($list) === '') {
        } elseif (isset($this->model_registry['fields']['status'])) {
            $this->query->where('column', $prefix . '.' . 'status', 'IN', 'integer', $list);
        }

        $this->query->where('column', 'catalog.redirect_to_id', '=', 'integer', 0);
        $this->query->where('column', $prefix . '.id', '<>', 'column', $prefix . '.catalog_type_id');

        return $this;
    }

    /**
     * Grid Query Pagination
     *
     * @param   array $parameters
     *
     * @return  $this
     * @since   1.0.0
     */
    protected function getGridDataQueryPagination($parameters)
    {
        $prefix = $this->query->getModelRegistry('primary_prefix');

        $this->getGridDataQueryPaginationOrdering($parameters);
        $this->getGridDataQueryPaginationDirection($parameters);

        $this->query->orderBy($prefix . '.' . $this->ordering, $this->direction);

        $this->getGridDataQueryPaginationOffset($parameters);
        $this->query->setModelRegistry('model_offset', $this->offset);

        $this->getGridDataQueryPaginationItemsPerPage($parameters);

        $this->query->setModelRegistry('model_count', $this->items_per_page);
        $this->query->setModelRegistry('model_use_pagination', 1);

        return $this;
    }

    /**
     * Grid Query Pagination
     *
     * @param   array $parameters
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
        if ((int)$parameters->model_offset === 0) {
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
