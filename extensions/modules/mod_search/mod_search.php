<?php
/**
 * @version		$Id: mod_search.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	mod_search
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$lang = MolajoFactory::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

$button			= $parameters->get('button', '');
$imagebutton	= $parameters->get('imagebutton', '');
$button_pos		= $parameters->get('button_pos', 'left');
$button_text	= htmlspecialchars($parameters->get('button_text', MolajoText::_('MOD_SEARCH_SEARCHBUTTON_TEXT')));
$width			= intval($parameters->get('width', 20));
$maxlength		= $upper_limit;
$text			= htmlspecialchars($parameters->get('text', MolajoText::_('MOD_SEARCH_SEARCHBOX_TEXT')));
$label			= htmlspecialchars($parameters->get('label', MolajoText::_('MOD_SEARCH_LABEL_TEXT')));
$set_Itemid		= intval($parameters->get('set_itemid', 0));
$layout_class_suffix = htmlspecialchars($parameters->get('layout_class_suffix'));

if ($imagebutton) {
	$img = modSearchHelper::getSearchImage($button_text);
}
$mitemid = $set_Itemid > 0 ? $set_Itemid : JRequest::getInt('Itemid');
require MolajoModuleHelper::getLayoutPath('mod_search', $parameters->get('layout', 'default'));
