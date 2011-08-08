<?php
/**
 * @version		$Id: installer.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_installer
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * * @since		1.0
 */

// no direct access
defined('_JEXEC') or die;

// Access check.
if (!MolajoFactory::getUser()->authorise('core.manage', 'com_installer')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

$controller	= JController::getInstance('Installer');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();