<?php
/**
 * @version		$Id: framework.php 21438 2011-06-04 13:35:56Z dextercowley $
 * @package		Joomla.Site
 * @subpackage	Application
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

//
// Joomla system checks.
//
error_reporting(E_ALL);
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

/*
 * Check for existing configuration file.
 */
if (file_exists(JPATH_CONFIGURATION.'/configuration.php') && (filesize(JPATH_CONFIGURATION.'/configuration.php') > 10) && !file_exists(JPATH_INSTALLATION.'/index.php')) {
	header('Location: ../index.php');
	exit();
}

//
// Joomla system startup.
//

// Import the cms version library if necessary.
if (!class_exists('MolajoVersion')) {
	require JPATH_ROOT.'/includes/version.php';
}

// Bootstrap the Joomla Framework.
require_once JPATH_LIBRARIES.'/import.php';

// System configuration.
$error_reporting = 1;

if ($error_reporting === 0) {
	error_reporting(0);
} else if ($error_reporting > 0) {
	error_reporting(1);
}

define('JDEBUG', 1);

//
// Joomla framework loading.
//

// System profiler.
if (JDEBUG) {
	jimport('joomla.error.profiler');
	$_PROFILER = JProfiler::getInstance('Installer');
}

// Joomla library imports.
jimport('joomla.database.table');
jimport('joomla.user.user');
jimport('joomla.environment.uri');
jimport('joomla.filter.filterinput');
jimport('joomla.filter.filteroutput');
jimport('joomla.html.parameter');
jimport('joomla.utilities.utility');
jimport('joomla.language.language');
jimport('joomla.utilities.string');
jimport('joomla.utilities.arrayhelper');
