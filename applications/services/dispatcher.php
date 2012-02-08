<?php
/**
 * @package     Molajo
 * @subpackage  Service
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */
defined('MOLAJO') or die;

/**
 * Dispatcher
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
class MolajoDispatcherService
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
            self::$instance = new MolajoDispatcherService();
        }
        return self::$instance;
    }

    /**
     * __construct
     *
     * Class constructor.
     *
     * @return boolean
     * @since  1.0
     */
    public function __construct()
    {
    }

    /**
     * Dispatcher
     *
     * @var    object
     * @since  1.0
     */
    protected $_dispatcher;

    /**
     * loadDispatcher
     *
     * Method to create an event _dispatcher for the Web application.  The logic and options for creating
     * this object are adequately generic for default cases but for many applications it will make sense
     * to override this method and create event _dispatchers based on more specific needs.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function loadDispatcher()
    {
        //        $this->_dispatcher = JDispatcher::getInstance();
    }

    /**
     * registerEvent
     *
     * Registers a handler to a particular event group.
     *
     * @param   string    $event    The event name.
     * @param   callback  $handler  The handler, a function or an instance of a event object.
     *
     * @return  Application  Instance of $this to allow chaining.
     *
     * @since   1.0
     */
    public function registerEvent($event, $handler)
    {
        //        if ($this->_dispatcher instanceof JDispatcher) {
        //            $this->_dispatcher->register($event, $handler);
        //        }

        return $this;
    }

    /**
     * triggerEvent
     *
     * Calls all handlers associated with an event group.
     *
     * @param   string  $event  The event name.
     * @param   array   $args   An array of arguments (optional).
     *
     * @return  array   An array of results from each function call, or null if no _dispatcher is defined.
     *
     * @since   1.0
     */
    public function triggerEvent($event, array $args = null)
    {
        //        if ($this->_dispatcher instanceof JDispatcher) {
        //            return $this->_dispatcher->trigger($event, $args);
        //        }

        return null;
    }
}
