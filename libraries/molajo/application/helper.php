<?php
/**
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

//todo: amy set defaults per application

/**
 * Molajo helper class.
 *
 * @package		Joomla.Administrator
 * @subpackage	Application
 */
class MolajoHelper
{
	/**
	 * Return the application option string
	 *
	 * @return	string		Option.
	 * @since	1.0
	 */
	public static function findOption()
	{
		$option = strtolower(JRequest::getCmd('option', ''));
		JRequest::setVar('option', $option);
		return $option;
	}
}