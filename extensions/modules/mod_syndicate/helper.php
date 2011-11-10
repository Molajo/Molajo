<?php
/**
 * @version		$Id: helper.php 20228 2011-01-10 00:52:54Z eddieajau $
 * @package		Joomla.Site
 * @subpackage	mod_syndicate
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

class modSyndicateHelper
{
	static function getLink(&$parameters)
	{
		$document = MolajoFactory::getDocument();

		foreach($document->_links as $link)
		{
			if (strpos($link, 'application/'.$parameters->get('format').'+xml')) {
				preg_match("#href=\"(.*?)\"#s", $link, $matches);
				return $matches[1];
			}
		}

	}
}