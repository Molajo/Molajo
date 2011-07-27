<?php
/**
 * @version		$Id: default.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	com_login
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Get the login modules
$modules = MolajoModuleHelper::getModules('login');

foreach ($modules as $module)
// Render the login modules
	echo MolajoModuleHelper::renderModule($module, array('style' => 'rounded', 'id' => 'section-box'));

