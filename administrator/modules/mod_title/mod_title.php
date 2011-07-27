<?php
/**
 * @version		$Id: mod_title.php 20196 2011-01-09 02:40:25Z ian $
 * @package		Joomla.Administrator
 * @subpackage	mod_title
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

// Get the component title div
$title = MolajoFactory::getApplication()->get('JComponentTitle');

require MolajoModuleHelper::getLayoutPath('mod_title', $params->get('layout', 'default'));