<?php
/**
 * @version		$Id: mod_footer.php 18629 2010-08-25 04:46:03Z eddieajau $
 * @package		Joomla.Site
 * @subpackage	mod_footer
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('JPATH_PLATFORM') or die;

$app		= MolajoFactory::getApplication();
$date		= MolajoFactory::getDate();
$cur_year	= $date->format('Y');
$csite_name	= $app->getCfg('sitename');

if (JString::strpos(JText :: _('MOD_FOOTER_LINE1'), '%date%')) {
	$line1 = str_replace('%date%', $cur_year, JText :: _('MOD_FOOTER_LINE1'));
}
else {
	$line1 = JText :: _('MOD_FOOTER_LINE1');
}

if (JString::strpos($line1, '%sitename%')) {
	$lineone = str_replace('%sitename%', $csite_name, $line1);
}
else {
	$lineone = $line1;
}

require MolajoModuleHelper::getLayoutPath('mod_footer', $params->get('layout', 'default'));
