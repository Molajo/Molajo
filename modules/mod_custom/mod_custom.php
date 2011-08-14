<?php
/**
 * @version		$Id: mod_custom.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Site
 * @subpackage	mod_custom
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

if ($params->def('prepare_content', 1))
{
	JPluginHelper::importPlugin('content');
	$module->content = JHtml::_('content.prepare', $module->content);
}

$layout_class_suffix = htmlspecialchars($params->get('layout_class_suffix'));

require MolajoModuleHelper::getLayoutPath('mod_custom', $params->get('layout', 'default'));
