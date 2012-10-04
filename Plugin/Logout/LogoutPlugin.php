<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
namespace Molajo\Plugin\Logout;

use Molajo\Plugin\Plugin\Plugin;;

defined('MOLAJO') or die;

/**
 * Logout
 *
 * @package     Molajo
 * @subpackage  Plugin
 * @since       1.0
 */
class LogoutPlugin extends Plugin
{

	/**
	 * Before Authenticating the Logout Process
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onBeforeLogout()
	{
		return false;
	}

	/**
	 * After Authenticating the Logout Process
	 *
	 * @return boolean
	 * @since   1.0
	 */
	public function onAfterLogout()
	{
		return false;
	}
}
