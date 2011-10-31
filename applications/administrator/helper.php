<?php
/**
 * @version		$Id: helper.php 20196 2011-01-09 02:40:25Z ian $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;

/**
 * Molajo Administrator Application helper class.
 * Provide many supporting API functions.
 *
 * @package		Joomla.Administrator
 * @subpackage	Application
 */
class MolajoAdministratorHelper extends MolajoHelper 
{
	/**
	 * Return the application option string [main component].
	 *
	 * @return	string		Option.
	 * @since	1.0
	 */
	public static function findOption()
	{
		$option = strtolower(JRequest::getCmd('option', ''));

		$user = MolajoFactory::getUser();

		if ($user->get('guest')) {
			$option = 'com_login';
		}

		if ($option == '') {
			$option = 'com_dashboard';
		}

		JRequest::setVar('option', $option);
		return $option;
	}
}