<?php
/**
 * @package     Joomla.Platform
 * @subpackage  User
 *
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('JPATH_PLATFORM') or die;

/**
 * Authenthication class, provides an interface for the Joomla authentication system
 *
 * @package     Joomla.Platform
 * @subpackage  User
 * @since       11.1
 */
class JAuthentication
{

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
		JError::raiseError('500', JText::_('MOLAJO_AUTHENTICIAN_IN_MOLAJOCONTROLLERLOGIN'));
	}

	/**
	 * Returns the global authentication object, only creating it
	 * if it doesn't already exist.
	 *
	 * @return  object  The global JAuthentication object
	 * @since   11.1
	 */
	public static function getInstance() {}

	public function authenticate($credentials, $options) {}

}

/**
 * Authorisation response class, provides an object for storing user and error details
 *
 * @package     Joomla.Platform
 * @subpackage  User
 * @since       11.1
 */
class JAuthenticationResponse extends MolajoAuthentication {}