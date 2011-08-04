<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2010-2011 Amy Stephen. All rights reserved. See http://Molajo.org/Copyright
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined( '_JEXEC' ) or die( 'Restricted access' );
define('MOLAJO', true);

require_once dirname(__FILE__).'/errorlog.php';
//trigger_error('errorlog.php has been included.');

/** Define Molajo Constants **/
define('JPATH_BASE', dirname(__FILE__));
$parts = explode(DS, JPATH_BASE);
define('JPATH_ROOT',		implode(DS, $parts));
define('JPATH_SITE',		JPATH_ROOT);
define('JPATH_CONFIGURATION',	JPATH_ROOT);
define('JPATH_ADMINISTRATOR',	JPATH_ROOT.DS.'administrator');
define('JPATH_LIBRARIES',	JPATH_ROOT.DS.'libraries');
define('JPATH_PLUGINS',		JPATH_ROOT.DS.'plugins'  );
define('JPATH_INSTALLATION',	JPATH_ROOT.DS.'installation');
define('JPATH_THEMES',		JPATH_BASE.DS.'templates');
define('JPATH_CACHE',		JPATH_BASE.DS.'cache');
define('JPATH_MANIFESTS',	JPATH_ADMINISTRATOR.DS.'manifests');

/** Run a portion of framework.php in order to register class overrides **/
//
// Joomla system checks.
//

@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

//
// Installation check, and check on removal of the install directory.
//

if (!file_exists(JPATH_CONFIGURATION.DS.'configuration.php') || (filesize(JPATH_CONFIGURATION.DS.'configuration.php') < 10) /*|| file_exists(JPATH_INSTALLATION.DS.'index.php')*/) {

	if (file_exists(JPATH_INSTALLATION.DS.'index.php')) {
		header('Location: '.substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'index.php')).'installation/index.php');
		exit();
	} else {
		echo 'No configuration file found and no installation code available. Exiting...';
		exit();
	}
}

// Load the loader class.
if (!class_exists('JLoader')) {
	require_once JPATH_LIBRARIES.DS.'loader.php';
}


/** Class Overrides **/
JLoader::register('JUser', JPATH_PLUGINS.'/molajo/libraries/joomla/user/user.php');