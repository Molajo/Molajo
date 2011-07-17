<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Installation Database Helper Class.
 *
 * @static
 * @package		Molajo
 * @subpackage  Installation
 * @since		1.0
 */
class JInstallationHelperDatabase
{
	/**
     * getDBO
     *
	 * Method to get a JDatabase object.
	 *
	 * @param	string	$driver		The database driver to use.
	 * @param	string	$host		The hostname to connect on.
	 * @param	string	$user		The user name to connect with.
	 * @param	string	$password	The password to use for connection authentication.
	 * @param	string	$database	The database to use.
	 * @param	string	$prefix		The table prefix to use.
	 * @param	boolean $select		True if the database should be selected.
	 *
	 * @return	mixed	JDatabase object on success, JException on error.
	 * @since	1.0
	 */
	public static function & getDBO($driver, $host, $user, $password, $database, $prefix, $select = true)
	{
		static $db;

		if (!$db) {
			// Build the connection options array.
			$options = array (
				'driver' => $driver,
				'host' => $host,
				'user' => $user,
				'password' => $password,
				'database' => $database,
				'prefix' => $prefix,
				'select' => $select
			);

			// Get a database object.
			$db = JDatabase::getInstance($options);
		}

		return $db;
	}
}