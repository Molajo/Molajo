<?php
/**
 * @version		$Id: mod_toolbar.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	mod_toolbar
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Import dependancies.
jimport('joomla.html.toolbar');


/**
 * Toolbar
 */
if ($this->state->get('component_task') == 'add') {
    $set = 'config_manager_editor_button_bar_new_option';
} else if ($this->state->get('component_task') == 'edit') {
    $set = 'config_manager_editor_button_bar_edit_option';
} else {
    $set = 'config_manager_button_bar_option';
}
$aclClass = ucfirst($this->state->get('request.DefaultView')).'ACL';
$acl = new $aclClass ();
$this->permissions = $acl->getUserPermissionSet ($this->state->get('request.option'),
                                                 $this->state->get('request.EditView'),
                                                 $set);

$toolbar = new MolajoToolbarHelper ();
$toolbar->addButtonsDefaultLayout ($this->state->get('filter.option'), $this->permissions);

// Get the toolbar.
$toolbar = JToolBar::getInstance('toolbar')->render('toolbar');

require MolajoModuleHelper::getLayoutPath('mod_toolbar', $params->get('layout', 'default'));