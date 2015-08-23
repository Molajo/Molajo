<?php
/**
 * Item Plugin
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace Molajo\Plugins\Item;

use CommonApi\Event\InitialiseEventInterface;
use CommonApi\Event\CreateEventInterface;
use CommonApi\Event\ReadEventInterface;
use CommonApi\Event\UpdateEventInterface;
use CommonApi\Event\DeleteEventInterface;
use Molajo\Plugins\DeleteEvent;

/**
 * Item Plugin
 *
 * @package  Molajo
 * @license  http://www.opensource.org/licenses/mit-license.html MIT License
 * @since    1.0
 */
final class ItemPlugin extends DeleteEvent
    implements InitialiseEventInterface, CreateEventInterface, ReadEventInterface, UpdateEventInterface, DeleteEventInterface
{
    /**
     * onAfterReadRow - processed once for each row read
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterReadRow()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        return $this;
    }

    /**
     * Should plugin be executed?
     *
     * @return  boolean
     * @since   1.0.0
     */
    protected function checkProcessPlugin()
    {
        if (is_object($this->controller['row'])) {
        } else {
            return false;
        }

        if (is_object($this->controller['query'])) {
        } else {
            return false;
        }

        return true;
    }

    /**
     * Pre-initialise row processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeInitialise()
    {
        return $this;
    }

    /**
     * Post-initialise row processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterInitialise()
    {
        return $this;
    }

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

        $now = $this->controller['query']->getDate();

        $this->controller['row']->created_datetime = $now;
        $this->controller['row']->created_by       = $this->runtime_data->user->id;

        if (isset($this->controller['row']->checked_out_datetime)) {
            $this->controller['row']->checked_out_datetime = $this->controller['query']->getNullDate();
            $this->controller['row']->checked_out_by       = 0;
        }

        if (isset($this->controller['row']->status) && $this->controller['row']->status > 0) {
            if ($this->controller['row']->start_publishing_datetime === $this->controller['query']->getNullDate()) {
                $this->controller['row']->start_publishing_datetime = $now;
            }
        }

        return $this;
    }

    /**
     * Post-create processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterCreate()
    {
        return $this;
    }

    /**
     * Before update processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeUpdate()
    {
        if ($this->checkProcessPlugin() === false) {
            return $this;
        }

        $this->controller['row']->last_updated_datetime = $this->controller['query']->getDate();
        $this->controller['row']->last_updated_by       = $this->runtime_data->user->id;

        if (isset($this->controller['row']->checked_out_datetime)) {
            $this->controller['row']->checked_out_datetime = $this->controller['query']->getNullDate();
            $this->controller['row']->checked_out_by       = 0;
        }

        return $this;
    }

    /**
     * After update processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterUpdate()
    {
        return $this;
    }

    /**
     * Before delete processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onBeforeDelete()
    {
        return $this;
    }

    /**
     * After delete processing
     *
     * @return  $this
     * @since   1.0.0
     */
    public function onAfterDelete()
    {
        return $this;
    }
}
