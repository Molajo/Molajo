<?php
/**
 * @package     Molajo
 * @subpackage  Module
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** format */
$session = MolajoFactory::getSession();
if ($session->get('page.format') == 'html') {
} else {
    return;
}

/** type */
if ($session->get('page.task') == 'add') {
    $buttonSet = 'config_manager_editor_button_bar_new_option';
} else if ($session->get('page.task') == 'edit') {
    $buttonSet = 'config_manager_editor_button_bar_edit_option';
} else {
    $buttonSet = 'config_manager_button_bar_option';
}

$acl = new MolajoACL ();
$permissions = $acl->getUserPermissions($session->get('page.option'), $session->get('page.view'), $buttonSet);

/** Build the Toolbar
$toolbar = new MolajoToolbarHelper ();
$toolbar->addButtonsDisplayView ($session->get('page.option'), $permissions);
 */
/**
$toolbar = MolajoToolbar::getInstance('toolbar')->render('toolbar');
require MolajoModule::getViewPath('toolbar', $parameters->get('view', 'default'));
 */
// wrap div == cf
$request['wrap'] = $module->style;
$request['position'] = $module->position;
$request['view'] = 'admintitle';
$request['view_type'] = 'extension';