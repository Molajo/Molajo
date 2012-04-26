<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service;

defined('MOLAJO') or die;

/**
 * Dispatcher
 *
 * Events
 *
 * Initialisation
 *
 * .. Services
 * .. .. Initiate
 * .. .. .. User
 * .. .. .. .. Session
 * .. .. .. .. Authenticate
 * .. .. .. .. Logout
 * Route
 * Authorise
 * Action
 * .. Display
 * .. .. Parse
 * .. .. Include
 * .. .. .. Controller
 * .. .. .. Query
 * .. .. .. Render
 * .. .. .. .. Template
 * .. .. .. .. Wrap
 *
 * @package   Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class DispatcherService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Dispatcher
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $_dispatcher;

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
			self::$instance = new DispatcherService();
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
	protected function __construct()
	{
	}

	/**
	 * loadDispatcher
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function loadDispatcher()
	{
		// Startup should load the dispatcher
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
		//not used
		//        if ($this->_dispatcher instanceof JDispatcher) {
		//            $this->_dispatcher->register($event, $handler);
		//        }

		return $this;
	}

	/**
	 * notify
	 *
	 * Notifies services listening for a specific event
	 *
	 * @param   string  $event  Event
	 * @param   array   $args   An array of arguments (optional).
	 *
	 * @return  array   An array of results from each function call, or null if no _dispatcher is defined.
	 *
	 * @since   1.0
	 */
	public function notify($event, array $args = null)
	{
		//        if ($this->_dispatcher instanceof JDispatcher) {
		//            return $this->_dispatcher->trigger($event, $args);
		//        }

		return null;
	}
}
