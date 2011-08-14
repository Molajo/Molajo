<?php
/**
 * @version		$Id: mod_whosonline.php 21084 2011-04-05 00:49:22Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_whosonline
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the whosonline functions only once
require_once dirname(__FILE__).'/helper.php';

$showmode = $params->get('showmode', 0);

if ($showmode == 0 || $showmode == 2) {
	$count	= modWhosonlineHelper::getOnlineCount();
}

if ($showmode > 0) {
	$names	= modWhosonlineHelper::getOnlineUserNames();
}

$linknames = $params->get('linknames', 0);
$layout_class_suffix = htmlspecialchars($params->get('layout_class_suffix'));

require MolajoModuleHelper::getLayoutPath('mod_whosonline', $params->get('layout', 'default'));