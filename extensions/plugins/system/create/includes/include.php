<?php
/**
 * @version     $id: start.php
 * @package     Molajo
 * @subpackage  Installer Overrides for New Create Extensions Feature
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** Primary Controller is overridden for two reasons:
 *
 *  1. The following line had to be commented out to load another version of the Sub-Menu
 *      require_once JPATH_COMPONENT.'/helpers/installer.php';
 *      Core should check if the class exists and only load the file if it doesn't
 *
 *  2. For whatever reason. the new Create Controller was not found by the Installer Component.
 *      The primary controller now has the Create Method for the new Molajo feature.
 *      Need to figure out why this is happening.
 */

/** load the File Helper **/
if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(COM_INSTALLER_OVERRIDES.'/com_installer/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', COM_INSTALLER_OVERRIDES.'/com_installer/helpers/file.php');
    } else {
        JError::raiseNotice(500, JText::_('PLG_SYSTEM_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
$filehelper = new MolajoFileHelper();

/** Override Primary Controller **/
$filehelper->requireClassFile (COM_INSTALLER_OVERRIDES.'/com_installer/controllers/controller.php', 'InstallerController');

/** Override Helper to add Create Submenu **/
$filehelper->requireClassFile (COM_INSTALLER_OVERRIDES.'/com_installer/helpers/installer.php', 'InstallerHelper');

/** JHtml Classes **/
$filehelper->requireClassFile (COM_INSTALLER_OVERRIDES.'/jhtml/molajocomponent.php', 'JHtmlMolajoComponent');
$filehelper->requireClassFile (COM_INSTALLER_OVERRIDES.'/jhtml/plugintype.php', 'JHtmlPluginType');

/** JForm Classes **/
$filehelper->requireClassFile (COM_INSTALLER_OVERRIDES.'/jform/spacer.php', 'JFormFieldSpacer');

/** Add new Model **/
$filehelper->requireClassFile (COM_INSTALLER_OVERRIDES.'/com_installer/models/create.php', 'InstallerModelCreate');

/** Add new View **/
$filehelper->requireClassFile (COM_INSTALLER_OVERRIDES.'/com_installer/views/create/view.html.php', 'InstallerViewCreate');

/** Load Dependencies **/
jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.installer.installer');
jimport('joomla.installer.helper');
jimport('joomla.client.helper');