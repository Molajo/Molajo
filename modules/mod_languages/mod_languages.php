<?php
/**
 * @version		$Id: mod_languages.php 20988 2011-03-18 08:13:24Z infograf768 $
 * @package		Joomla.Site
 * @subpackage	mod_languages
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__).'/helper.php';

$headerText	= JString::trim($params->get('header_text'));
$footerText	= JString::trim($params->get('footer_text'));

$cacheid = md5(JRequest::getVar('lang').$module->module);

$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'modLanguagesHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = $params;
$cacheparams->modeparams = $cacheid;

$list = MolajoModuleHelper::moduleCache ($module, $params, $cacheparams);

$layout_class_suffix = htmlspecialchars($params->get('layout_class_suffix'));

require MolajoModuleHelper::getLayoutPath('mod_languages', $params->get('layout', 'default'));
