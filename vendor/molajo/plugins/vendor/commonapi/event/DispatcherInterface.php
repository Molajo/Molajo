<?php
/**
 * Dispatcher Interface
 *
 * @package    Event
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
namespace CommonApi\Event;

/**
 * Dispatcher Interface
 *
 * @package    Event
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @since      1.0
 */
interface DispatcherInterface
{
    /**
     * Listener registers for an Event with the Dispatcher
     *
     * @param   string $event_name
     * @param   object $callback
     * @param   int    $priority 0 (lowest) to 100 (highest)
     *
     * @return  mixed
     * @since   1.0.0
     */
    public function registerForEvent($event_name, $callback, $priority = 50);

    /**
     * Requester Schedules Event with Dispatcher
     *
     * @param   string         $event_name
     * @param   EventInterface $event      CommonApi\Event\EventInterface
     *
     * @return  $this
     * @since   1.0.0
     */
    public function scheduleEvent($event_name, EventInterface $event);
}
