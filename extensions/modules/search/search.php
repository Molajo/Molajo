<?php
/**
 * @version        $Id: search.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    search
 * @copyright    Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$lang = MolajoController::getLanguage();
$upper_limit = $lang->getUpperLimitSearchWord();

$button = $parameters->get('button', '');
$imagebutton = $parameters->get('imagebutton', '');
$button_pos = $parameters->get('button_pos', 'left');
$button_text = htmlspecialchars($parameters->get('button_text', MolajoTextHelper::_('SEARCH_SEARCHBUTTON_TEXT')));
$width = intval($parameters->get('width', 20));
$maxlength = $upper_limit;
$text = htmlspecialchars($parameters->get('text', MolajoTextHelper::_('SEARCH_SEARCHBOX_TEXT')));
$label = htmlspecialchars($parameters->get('label', MolajoTextHelper::_('SEARCH_LABEL_TEXT')));
$view_class_suffix = htmlspecialchars($parameters->get('view_class_suffix'));

if ($imagebutton) {
    $img = modSearchHelper::getSearchImage($button_text);
}
require MolajoModule::getViewPath('search', $parameters->get('view', 'default'));
