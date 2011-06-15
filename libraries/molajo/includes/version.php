<?php
/**
 * @version     $id: version.php
 * @package     Molajo
 * @subpackage  Application
 * @copyright   Copyright (C) 2011 Individual Molajo Contributors. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/**
 * Version information class for Molajo
 *
 * @package  Molajo
 * @since    1.0
 */
final class MolajoApplication
{
	// Product name.
	const PRODUCT = 'Molajo';
	// Release version.
	const RELEASE = '1';
	// Maintenance version.
	const MAINTENANCE = '0';
	// Development STATUS.
	const STATUS = 'Alpha';
	// Build number.
	const BUILD = 0;
	// Code name.
	const CODE_NAME = 'Phoenix';
	// Release date.
	const RELEASE_DATE = '01-Jul-2011';
	// Release time.
	const RELEASE_TIME = '06:00';
	// Release timezone.
	const RELEASE_TIME_ZONE = 'GMT';
	// Copyright Notice.
	const COPYRIGHT = 'Copyright (C) 2011 Individual Contributors to the Molajo Project. All rights reserved.';
	// Link text.
	const LINK_TEXT = '<a href="http://molajo.org">Molajo</a> is Free Software released under the GNU General Public License.';

	/**
	 * Compares two a "PHP standardized" version number against the current version.
	 *
	 * @param   string  $minimum  The minimum version of the Application which is compatible.
	 *
	 * @return  bool    True if the version is compatible.
	 *
	 * @see     http://www.php.net/version_compare
	 * @since   1
	 */
	public static function isCompatible($minimum)
	{
		return (version_compare(MolajoApplication, $minimum, 'eq') == 1);
	}

	/**
	 * Gets a "PHP standardized" version string for the current Joomla Platform.
	 *
	 * @return  string  Version string.
	 *
	 * @since   1
	 */
	public static function getShortVersion()
	{
		return self::RELEASE.'.'.self::MAINTENANCE;
	}

	/**
	 * Gets a version string for the current Joomla Platform with all release information.
	 *
	 * @return  string  Complete version string.
	 *
	 * @since   1
	 */
	public static function getLongVersion()
	{
		return self::PRODUCT.' '. self::RELEASE.'.'.self::MAINTENANCE.' '
				. self::STATUS.' [ '.self::CODE_NAME.' ] '.self::RELEASE_DATE.' '
				.self::RELEASE_TIME.' '.self::RELEASE_TIME_ZONE;
	}
}
