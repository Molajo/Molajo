<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Event Manager
 *
 * Application and Controller => Schedule Events
 *
 * Triggers => Register for Events
 *
 * Event Manager => Coordinates the two
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
Class EventService
{
	/**
	 * Static instance
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * Event
	 *
	 * @var    object
	 * @since  1.0
	 */
	protected $registeredEvents;

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
			self::$instance = new EventService();
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
	 * Register triggers with events
	 *
	 * @since   1.0
	 */
	public function register()
	{


		return $this;
	}

	/**
	 * Event occurs and registered triggers are fired
	 *
	 * @param  string  $event  			Name of the Event
	 * @param  array   $options   		Array of options to be passed on to triggers
	 * @param  string  $process_flag	0: process all events
	 * 									1: process all until one fails
	 * 									2: process each event until one succeeds
	 *
	 * @return  array  $results			Array of results from trigger methods
	 *
	 * @since   1.0
	 */
	public function fire($event, $options, $process = 0)
	{


		return null;
	}
}
