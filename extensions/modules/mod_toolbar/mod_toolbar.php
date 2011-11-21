<?php
/**
 * @package     Molajo
 * @subpackage  Submenu
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** 1. should the module run? */
$session = MolajoFactory::getSession();
if ($session->get('page.format') == 'html') {
} else {
    return;
}

/** ACL
if ($session->get('page.task') == 'add') {
    $set = 'config_manager_editor_button_bar_new_option';
} else if ($session->get('page.task') == 'edit') {
    $set = 'config_manager_editor_button_bar_edit_option';
} else {
    $set = 'config_manager_button_bar_option';
}

$acl = new MolajoACL ();
$permissions = $acl->getUserPermissionSet ($session->get('page.option'), $session->get('page.view'), $set);
*/
/** Build the Toolbar
$toolbar = new MolajoToolbarHelper ();
$toolbar->addButtonsDisplayLayout ($session->get('page.option'), $permissions);
*/
/**
$toolbar = MolajoToolbar::getInstance('toolbar')->render('toolbar');
require MolajoApplicationModule::getLayoutPath('mod_toolbar', $parameters->get('layout', 'default'));
*/
// wrap div == cf
$request['wrap'] = $module->style;
$request['position'] = $module->position;
$request['layout'] = 'admintitle';
$request['layout_type'] = 'extension';