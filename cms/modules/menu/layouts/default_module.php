<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$renderer	= MolajoFactory::getDocument()->loadRenderer('module');
$module		= MolajoApplicationModule::getModule('layout');
$module->parameters	= "wrap=section\nlayout=admin_dashboard";
$capture = $renderer->render($module);
echo $capture;

