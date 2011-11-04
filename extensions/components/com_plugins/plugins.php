<?php
/**
 * @version		$Id: plugins.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_plugins
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Access check.
if (!MolajoFactory::getUser()->authorise('core.manage', 'com_plugins')) {
	return MolajoError::raiseWarning(404, MolajoText::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

// Create the controller
$controller	= JController::getInstance('Plugins');
$controller->execute(JRequest::getCmd('task'));
$controller->redirect();