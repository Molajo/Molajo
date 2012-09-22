<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Application;

use Molajo\Plugin\Plugin\Plugin;
use Molajo\Service\Services;

defined('MOLAJO') or die;

/**
 * Application Base
 *
 * Application events are scheduled one, and only one time, per page load
 * Standard system variables, like Molajo::Registry()->('parameters') should
 * be used in Application Plugins since there is no danger of collusion
 * with other instances
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class ApplicationPlugin extends Plugin
{

	/**
	 * Runs before Route and after Services and Helpers have been instantiated
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterInitialise()
	{
		return true;
	}

	/**
	 * Follows Authorise and can used to override a failed authorisation or a successful one
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterAuthorise()
	{
		return true;
	}

	/**
	 * Event fires after execute for both display and non-display task
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterExecute()
	{
		return true;
	}

	/**
	 * Plugin that fires after all views are rendered
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterResponse()
	{
		return true;
	}

}
