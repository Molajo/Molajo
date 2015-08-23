<?php
/**
 * Ordering Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Ordering;

use Molajo\Plugins\UpdateEvent;
use CommonApi\Event\CreateEventInterface;
use CommonApi\Event\UpdateEventInterface;

/**
 * Ordering Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class OrderingPlugin extends UpdateEvent implements CreateEventInterface, UpdateEventInterface
{
    /**
     * Pre-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeCreate()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this->setOrdering();
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (isset($this->controller['row']->ordering)) {
        } else {
            return false;
        }

        if ((int)$this->controller['row']->ordering > 0) {
            return false;
        }

        return true;
    }

    /**
     * Set Ordering Value in Row
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function setOrdering()
    {
        $this->controller['row']->ordering = $this->getMaxOrdering() + 1;

        return $this;
    }

    /**
     * Get Maximum Ordering Value
     *
     * @return  integer
     * @since   1.0.0
     */
    protected function getMaxOrdering()
    {
        $options                 = array();
        $options['runtime_data'] = $this->runtime_data;

        $model_type = $this->controller['query']->getModelRegistry('model_type');
        $model_name = $this->controller['query']->getModelRegistry('model_name');
        $model      = 'Molajo//Model//' . $model_type . '//' . $model_name . '.xml';

        $this->setQueryController($model);

        $this->setQueryControllerDefaults(
            $process_events = 0,
            $query_object = 'result',
            $get_customfields = 0,
            $use_special_joins = 0,
            $use_pagination = 0,
            $check_view_level_access = 0,
            $get_item_children = 0
        );

        $prefix     = $this->query->getModelRegistry('primary_prefix', 'a');
        $table_name = $this->query->getModelRegistry('table_name');

        $sql = 'select max('
            . $this->query->escapeName($prefix)
            . '.'
            . $this->query->escapeName('ordering')
            . ') as '
            . $this->query->escapeName('maximum');

        $sql .= ' from '
            . $this->query->escapeName($table_name)
            . ' '
            . $this->query->escapeName($prefix);

        if (isset($this->controller['row']->catalog_type_id)) {
            $catalog_type_id = $this->controller['row']->catalog_type_id;

            $sql .= ' where '
                . $this->query->escapeName($prefix)
                . '.'
                . $this->query->escapeName('catalog_type_id')
                . ' = '
                . $this->query->escape($catalog_type_id);
        }

        return $this->runQuery('getData', $sql);
    }
}
