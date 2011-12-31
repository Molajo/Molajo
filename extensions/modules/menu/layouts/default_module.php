<?php
/**
 * @package     Molajo
 * @subpackage  Menu
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$renderer = MolajoController::getApplication()->loadRenderer('module');
$module = MolajoModule::getModule('view');
$module->parameters = "wrap=section\nview=dashboard";
$capture = $renderer->render($module);
echo $capture;

