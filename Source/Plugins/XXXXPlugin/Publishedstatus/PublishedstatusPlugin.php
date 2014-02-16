<?php
/**
 * Published Status Plugin
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Publishedstatus;

use CommonApi\Event\ReadInterface;
use Molajo\Plugins\ReadEventPlugin;

/**
 * Published Status Plugin
 *
 * @package     Molajo
 * @license     http://www.opensource.org/licenses/mit-license.html MIT License
 * @since       1.0
 */
class PublishedstatusPlugin extends ReadEventPlugin implements ReadInterface
{
    /**
     * Pre-read processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeRead()
    {
        if ($this->get('data_object', '') == 'Database') {
        } else {
            return $this;;
        }

        $field = $this->getField('start_publishing_datetime');
        if ($field === false) {
            return $this;
        }

        $field = $this->getField('stop_publishing_datetime');
        if ($field === false) {
            return $this;
        }

        $field = $this->getField('status');
        if ($field === false) {
            return $this;
        }

        $primary_prefix = $this->get('primary_prefix', 0);

        $this->model->query->where(
            $this->model->database->qn($primary_prefix)
            . '.' . $this->model->database->qn('status')
            . ' > ' . STATUS_UNPUBLISHED
        );

        $this->model->query->where(
            '(' . $this->model->database->qn($primary_prefix)
            . '.' . $this->model->database->qn('start_publishing_datetime')
            . ' = ' . $this->model->database->q($this->model->null_date)
            . ' OR ' . $this->model->database->qn($primary_prefix) . '.' . $this->model->database->qn(
                'start_publishing_datetime'
            )
            . ' <= ' . $this->model->database->q($this->model->now) . ')'
        );

        $this->model->query->where(
            '(' . $this->model->database->qn($primary_prefix)
            . '.' . $this->model->database->qn('stop_publishing_datetime')
            . ' = ' . $this->model->database->q($this->model->null_date)
            . ' OR ' . $this->model->database->qn($primary_prefix) . '.' . $this->model->database->qn(
                'stop_publishing_datetime'
            )
            . ' >= ' . $this->model->database->q($this->model->now) . ')'
        );

        return $this;
    }

    /**
     * Post-create processing
     *
     * @param $this ->row, $model
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterCreate()
    {
        // if it is published, notify
        return $this;
    }

    /**
     * Pre-update processing
     *
     * @param   $this ->row
     * @param   $model
     *
     * @return  $this
     * @since   1.0
     */
    public function onBeforeUpdate()
    {
        // hold status
        // if it is published (or greater) make certain published dates are ok
        // if status changes -- it should unpublished below
        return $this;
    }

    /**
     * Post-update processing
     *
     * @return  $this
     * @since   1.0
     */
    public function onAfterUpdate()
    {
        // if it wasn't published and now is

        // is email notification enabled? are people subscribed?
        // tweets
        // pings
        return $this;
    }

    /**
     *  Notify
     *
     * @return  $this
     * @since   1.0
     */
    public function notify()
    {
        // is email notification enabled? are people subscribed?
        // tweets
        // pings
    }
}
