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

// Get the toolbar.
$toolbar = JToolBar::getInstance('toolbar')->render('toolbar');

require JModuleHelper::getLayoutPath('mod_toolbar', $params->get('layout', 'default'));