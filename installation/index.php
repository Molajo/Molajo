<?php
/**
 * @version		$Id: index.php 21652 2011-06-23 05:33:52Z chdemko $
 * @package		Joomla.Installation
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

define('_JEXEC', 1);

define('JPATH_BASE', dirname(__FILE__));

define('DS', DIRECTORY_SEPARATOR);

if (file_exists(JPATH_BASE.'/defines.php')) {
	include_once JPATH_BASE.'/defines.php';
}
if (!defined('_MOLAJO_DEFINES')) {
	require_once dirname(__FILE__).'/includes/defines.php';
}
 
require_once JPATH_BASE.'/includes/framework.php';

// Create the application object.
$app = JFactory::getApplication('installation');

// Initialise the application.
$app->initialise();

// Render the document.
$app->render();

// Return the response.
echo $app;
