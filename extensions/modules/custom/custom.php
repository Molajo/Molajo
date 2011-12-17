<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$layout = $parameters->def('layout', 'custom');
$wrap = $parameters->def('wrap', 'div');

if ($parameters->def('prepare_content', 1)) {
    MolajoPlugin::importPlugin('content');
    MolajoHTML::_('content.prepare', $module->content);
}
$rowset[0]->content = $module->content;
