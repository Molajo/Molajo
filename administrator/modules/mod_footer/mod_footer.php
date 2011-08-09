<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Molajo. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$request['layout'] = $params->def('layout', 'adminfooter');
$request['wrap'] = $params->def('wrap', 'div');

/**
 *  Line 1
 */
$current_year	= MolajoFactory::getDate()->format('Y');
$csite_name	    = MolajoFactory::getApplication()->getCfg('sitename');

if (JString::strpos(JText :: _('MOD_FOOTER_LINE1'), '%date%')) {
	$line1 = str_replace('%date%', $current_year, JText :: _('MOD_FOOTER_LINE1'));
} else {
	$line1 = JText :: _('MOD_FOOTER_LINE1');
}

if (JString::strpos($line1, '%sitename%')) {
	$line1 = str_replace('%sitename%', $csite_name, $line1);
}
$rowset[0]->line1 = $line1;

/**
 *  Line 2
 */
$rowset[0]->link = $params->def('link', 'http://molajo.org');
$rowset[0]->linked_text = $params->def('linked_text', 'Molajo&#174;');
$rowset[0]->remaining_text = $params->def('remaining_text', 'is free software.');
if ($params->def('version', '')) {
    $rowset[0]->version = JText::_(MOLAJOVERSION). ' '.MOLAJOVERSION;
} else {
    $rowset[0]->version = $params->def('version', '');
}

$line2 = '<a href="'.$rowset[0]->link.'">'.$rowset[0]->linked_text.'</a>';
$line2 .= $rowset[0]->remaining_text;
$line2 .= $rowset[0]->version;

$rowset[0]->version = $line2;

