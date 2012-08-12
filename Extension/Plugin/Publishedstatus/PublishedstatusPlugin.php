<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Extension\Plugin\Publishedstatus;

use Molajo\Extension\Plugin\Content\ContentPlugin;

defined('MOLAJO') or die;

/**
 * Published Status
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class PublishedstatusPlugin extends ContentPlugin
{
    /**
     * Pre-read processing
     *
     * @param   $this->data
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeRead()
    {

        if (isset($this->parameters['action'])
            && $this->parameters['action'] == 'delete'
        ) {
            return true;
        }

        $primary_prefix = $this->get('primary_prefix');

        $this->query->where($this->db->qn($primary_prefix)
            . '.' . $this->db->qn('status')
            . ' > ' . STATUS_UNPUBLISHED);

        $this->query->where('(' . $this->db->qn($primary_prefix)
                . '.' . $this->db->qn('start_publishing_datetime')
                . ' = ' . $this->db->q($this->null_date)
                . ' OR ' . $this->db->qn($primary_prefix) . '.' . $this->db->qn('start_publishing_datetime')
                . ' <= ' . $this->db->q($this->now) . ')'
        );

        $this->query->where('(' . $this->db->qn($primary_prefix)
                . '.' . $this->db->qn('stop_publishing_datetime')
                . ' = ' . $this->db->q($this->null_date)
                . ' OR ' . $this->db->qn($primary_prefix) . '.' . $this->db->qn('stop_publishing_datetime')
                . ' >= ' . $this->db->q($this->now) . ')'
        );

        return true;
    }

    /**
     * Post-create processing
     *
     * @param $this->data, $model
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
     * @param   $this->data
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
     * @param   $this->data
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
