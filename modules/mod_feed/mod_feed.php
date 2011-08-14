<?php
/**
 * @version		$Id: mod_feed.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_feed
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$rssurl	= $params->get('rssurl', '');
$rssrtl	= $params->get('rssrtl', 0);

//check if feed URL has been set
if (empty ($rssurl))
{
	echo '<div>';
	echo JText::_('MOD_FEED_ERR_NO_URL');
	echo '</div>';
	return;
}

$feed = modFeedHelper::getFeed($params);
$layout_class_suffix = htmlspecialchars($params->get('layout_class_suffix'));

require MolajoModuleHelper::getLayoutPath('mod_feed', $params->get('layout', 'default'));
