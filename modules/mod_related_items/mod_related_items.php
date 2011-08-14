<?php
/**
 * @version		$Id: mod_related_items.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_related_items
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$cacheparams = new stdClass;
$cacheparams->cachemode = 'safeuri';
$cacheparams->class = 'modRelatedItemsHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = array('id'=>'int','Itemid'=>'int');

$list = MolajoModuleHelper::moduleCache ($module, $params, $cacheparams);

if (!count($list)) {
	return;
}

$layout_class_suffix = htmlspecialchars($params->get('layout_class_suffix'));
$showDate = $params->get('showDate', 0);

require MolajoModuleHelper::getLayoutPath('mod_related_items', $params->get('layout', 'default'));
