<?php
/**
 * @version     $id: include.php
 * @package     Molajo
 * @subpackage  Installer Overrides for New Create Extensions Feature
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * com_config overrides to load Parameter XML from one source
 */

if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(MOLAJO_LIBRARY.'/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', MOLAJO_LIBRARY.'/helpers/file.php');
    } else {
        JError::raiseNotice(500, JText::_('PLG_SYSTEM_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
$filehelper = new MolajoFileHelper();
/** Override Item Model to add Form Events - will go away when we replace the entire component **/
$filehelper->requireClassFile (MOLAJO_LIBRARY.'/com_menus/models/item.php', 'MenusModelItem');

/** Load Dependencies **/
jimport('joomla.application.component.controller');
jimport('joomla.application.component.model');
jimport('joomla.application.component.modelform');
jimport('joomla.application.component.view');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');