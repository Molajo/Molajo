<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $params->def('layout', 'custom');
$wrap = $params->def('wrap', 'div');

if ($params->def('prepare_content', 1)) {
	MolajoPluginHelper::importPlugin('content');
	JHtml::_('content.prepare', $module->content);
}
$rowset[0]->content = $module->content;

