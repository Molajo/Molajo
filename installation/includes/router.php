<?php
/**
 * @package     Molajo
 * @subpackage  Installation
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * Class to create and parse routes
 *
 * @package		Molajo
 * @subpackage  Installation
 * @since		1.0
 */
class MolajoRouterInstallation extends JObject
{
	/**
     * parse
     *
	 * Function to convert a route to an internal URI
	 *
	 * @return	boolean
	 * @since	1.5
	 */
	public function parse($url)
	{
		return true;
	}

	/**
     * build
     *
	 * Function to convert an internal URI to a route
	 *
	 * @param	string	$url	The internal URL
	 *
	 * @return	string	The absolute search engine friendly URL
	 * @since	1.5
	 */
	public function build($url)
	{
		$url = str_replace('&amp;', '&', $url);

		return new JURI($url);
	}
}