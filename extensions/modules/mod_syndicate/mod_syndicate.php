<?php
/**
 * @version		$Id: mod_syndicate.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_syndicate
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$parameters->def('text', 'Feed Entries');
$parameters->def('format', 'rss');

$link = modSyndicateHelper::getLink($parameters);

if (is_null($link)) {
	return;
}

$layout_class_suffix = htmlspecialchars($parameters->get('layout_class_suffix'));

$text = htmlspecialchars($parameters->get('text'));

require MolajoApplicationModule::getLayoutPath('mod_syndicate', $parameters->get('layout', 'default'));
