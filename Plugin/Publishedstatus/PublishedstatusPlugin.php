<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Publishedstatus;

use Molajo\Plugin\Plugin\Plugin;

defined('NIAMBIE') or die;

/**
 * Published Status
 *
 * @package     Niambie
 * @license     GNU GPL v 2, or later and MIT
 * @since       1.0
 */
class PublishedstatusPlugin extends Plugin
{
    /**
     * Pre-read processing
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeRead()
    {
        if ($this->get('data_object', '', 'model_registry') == 'Database') {
        } else {
            return true;;
        }

        $field = $this->getField('start_publishing_datetime');
        if ($field === false) {
            return true;
        }

        $field = $this->getField('stop_publishing_datetime');
        if ($field === false) {
            return true;
        }

        $field = $this->getField('status');
        if ($field === false) {
            return true;
        }

        $primary_prefix = $this->get('primary_prefix', 0, 'model_registry');

        $this->model->query->where($this->model->db->qn($primary_prefix)
            . '.' . $this->model->db->qn('status')
            . ' > ' . STATUS_UNPUBLISHED);

        $this->model->query->where('(' . $this->model->db->qn($primary_prefix)
                . '.' . $this->model->db->qn('start_publishing_datetime')
                . ' = ' . $this->model->db->q($this->model->null_date)
                . ' OR ' . $this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn('start_publishing_datetime')
                . ' <= ' . $this->model->db->q($this->model->now) . ')'
        );

        $this->model->query->where('(' . $this->model->db->qn($primary_prefix)
                . '.' . $this->model->db->qn('stop_publishing_datetime')
                . ' = ' . $this->model->db->q($this->model->null_date)
                . ' OR ' . $this->model->db->qn($primary_prefix) . '.' . $this->model->db->qn('stop_publishing_datetime')
                . ' >= ' . $this->model->db->q($this->model->now) . ')'
        );

        return true;
    }

    /**
     * Post-create processing
     *
     * @param $this->row, $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterCreate()
    {
        // if it is published, notify
        return true;
    }

    /**
     * Pre-update processing
     *
     * @param   $this->row
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        // hold status
        // if it is published (or greater) make certain published dates are ok
        // if status changes -- it should unpublished below
        return true;
    }

    /**
     * Post-update processing
     *
     * @param   $this->row
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        // if it wasn't published and now is

        // is email notification enabled? are people subscribed?
        // tweets
        // pings
        return true;
    }

    public function notify()
    {
        // is email notification enabled? are people subscribed?
        // tweets
        // pings
    }
}
