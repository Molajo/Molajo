<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
namespace Molajo\Service\Services\Event;

use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Event
 *
 * @package     Molajo
 * @subpackage  Service
 * @since       1.0
 */
Class EventService
{
	/**
	 * @static
	 * @var    object
	 * @since  1.0
	 */
	protected static $instance;

	/**
	 * @static
	 * @return  bool|object
	 * @since   1.0
	 */
	public static function getInstance()
	{
		if (empty(self::$instance)) {
			self::$instance = new EventService();
		}
		return self::$instance;
	}

	/**
	 * application and controller schedule events with the event manager
	 *
	 * the event manager then fires off triggers which have registered for the event
	 *
	 * Usage:
	 * Services::Event()->schedule('onAfterDelete', $parameters);
	 *
	 * @param   string  $event
	 * @param   array   $parameters
	 *
	 * @return  boolean
	 *
	 * @since   1.0
	 */
	public function schedule($event, $parameters = array())
	{
		return $this;

		/** Retrieve Event Registrations */
		$exists = Services::Registry()->exists('Events', $event);

		if ($exists === true) {
			$registrations = Services::Registry()->get('Events', $event);
		} else {
			$registrations = array();
		}

		if (is_array($registrations)) {
		} else {
			if (trim($registrations) == '') {
				$registrations = array();
			} else {
				$temp = $registrations;
				$registrations = array();
				$registrations[] = $temp;
			}
		}

		if (count($registrations) > 0) {
			foreach ($registrations as $registration) {
				Services::Debug()->set('Event: ' . $event . ' fired registration by Trigger ' . $registration);
			}
		}
		return $parameters;
	}

	/**
	 * triggers register for events
	 *
	 * Usage:
	 * Services::Event()->register('onAfterRead', 'Author');
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function register($event, $trigger)
	{
		return $this;

		/** Retrieve Event Registrations */
		$exists = Services::Registry()->exists('Events', $event);

		if ($exists === true) {
			$registrations = Services::Registry()->get('Events', $event);
		} else {
			$registrations = array();
		}

		if (is_array($registrations)) {
		} else {
			if (trim($registrations) == '') {
				$registrations = array();
			} else {
				$temp = $registrations;
				$registrations = array();
				$registrations[] = $temp;
			}
		}

		$registrations[] = $trigger;

		Services::Registry()->set('Events', $event, $registrations);

		Services::Debug()->set('Trigger:' . $trigger . ' registered for Event: ' . $event);

		return $this;
	}
}
