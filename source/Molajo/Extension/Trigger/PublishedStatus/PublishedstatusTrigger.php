<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Extension\Trigger\Publishedstatus;

use Molajo\Extension\Trigger\Content\ContentTrigger;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Published Status
 *
 * @package     Molajo
 * @subpackage  Trigger
 * @since       1.0
 */
class PublishedstatusTrigger extends ContentTrigger
{
    /**
     * Static instance
     *
     * @var    object
     * @since  1.0
     */
    protected static $instance;

    /**
     * getInstance
     *
     * @static
     * @return bool|object
     * @since  1.0
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new PublishedstatusTrigger();
        }

        return self::$instance;
    }

    /**
     * Pre-read processing
     *
     * @param   $this->query_results
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeRead()
    {
        $primary_prefix = Services::Registry()->get($this->table_registry_name, 'primary_prefix', 'a');

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

        return $this;
    }

    /**
     * Pre-create processing
     *
     * @param   $this->query_results
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeCreate()
    {
        // if published or greater status
        // make certain published start date is today or later
        return true;
    }

    /**
     * Post-create processing
     *
     * @param $this->query_results, $model
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
     * @param   $this->query_results
     * @param   $model
     *
     * @return boolean
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        // hold status
        // if it is published (or greater) make certain published dates are ok
        return true;
    }

    /**
     * Post-update processing
     *
     * @param   $this->query_results
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