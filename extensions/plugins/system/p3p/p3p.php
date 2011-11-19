<?php
/**
 * @version		$Id: p3p.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('MOLAJO') or die;


/**
 * Molajo P3P Header Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.p3p
 */
class plgSystemP3p extends MolajoPlugin
{
	function onAfterInitialise()
	{
		// Get the header
		$header = $this->parameters->get('header','NOI ADM DEV PSAi COM NAV OUR OTRo STP IND DEM');
		$header = trim($header);
		// Bail out on empty header (why would anyone do that?!)
		if( empty($header) )
		{
			return;
		}
		// Replace any existing P3P headers in the response
		JResponse::setHeader('P3P','CP="'.$header.'"',true);
	}
}
