<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** MOLAJO_PATH_CONFIGURATION can be moved to hide the configuration.php file */
define('MOLAJO_PATH_CONFIGURATION', MOLAJO_PATH_ROOT);

define('MOLAJO_PATH_SITE',			MOLAJO_PATH_ROOT);
define('MOLAJO_PATH_ADMINISTRATOR',	MOLAJO_PATH_ROOT.'/administrator');
define('MOLAJO_PATH_PLUGINS',		MOLAJO_PATH_ROOT.'/plugins');
define('MOLAJO_PATH_INSTALLATION',	MOLAJO_PATH_ROOT.'/installation');
define('MOLAJO_PATH_CACHE',			MOLAJO_PATH_BASE.'/cache');
define('MOLAJO_LIBRARY',		    MOLAJO_PATH_ROOT.'/libraries/molajo');
define('MOLAJO_PATH_MANIFESTS',		MOLAJO_PATH_ADMINISTRATOR.'/manifests');

define('MOLAJO_LIBRARY_COMPONENT',  MOLAJO_LIBRARY.'/component');
define('MOLAJO_LIBRARY_CONTROLLERS', MOLAJO_LIBRARY_COMPONENT.'/controllers');
define('MOLAJO_LIBRARY_FIELDS',     MOLAJO_LIBRARY_COMPONENT.'/fields');
define('MOLAJO_LIBRARY_MODELS',     MOLAJO_LIBRARY_COMPONENT.'/models');
define('MOLAJO_LIBRARY_ROUTER',     MOLAJO_LIBRARY_COMPONENT.'/router');
define('MOLAJO_LIBRARY_TABLES',     MOLAJO_LIBRARY_COMPONENT.'/tables');
define('MOLAJO_LIBRARY_VIEWS',      MOLAJO_LIBRARY_COMPONENT.'/views');

define('MOLAJO_LIBRARY_ATTRIBUTES', MOLAJO_LIBRARY_COMPONENT.'/fields/attributes');
define('MOLAJO_LIBRARY_FIELDS',     MOLAJO_LIBRARY_COMPONENT.'/fields/fields');
define('MOLAJO_LIBRARY_FIELDTYPES', MOLAJO_LIBRARY_COMPONENT.'/fields/fieldtypes');
define('MOLAJO_LIBRARY_FORM',       MOLAJO_LIBRARY_COMPONENT.'/fields/form');
define('MOLAJO_PATH_THEMES',        MOLAJO_PATH_BASE.'/templates');

/** Layouts, Forms, and Parameters */
$temp = MOLAJO_PATH_ROOT.'/layouts';
define('MOLAJO_LAYOUTS', $temp);
$temp = MOLAJO_LAYOUTS.'/common';
define('MOLAJO_LAYOUTS_COMMON', $temp);
$temp = MOLAJO_LAYOUTS.'/extensions';
define('MOLAJO_LAYOUTS_EXTENSIONS', $temp);
$temp = MOLAJO_LAYOUTS.'/forms';
define('MOLAJO_LAYOUTS_FORMS', $temp);
$temp = MOLAJO_LAYOUTS.'/document';
define('MOLAJO_LAYOUTS_DOCUMENT', $temp);
$temp = MOLAJO_LAYOUTS.'/wraps';
define('MOLAJO_LAYOUTS_WRAPS', $temp);

$temp = MOLAJO_PATH_ROOT.'/layouts/parameters';
define('MOLAJO_LAYOUTS_PARAMETERS', $temp);

/** Table */
define('MOLAJO_CONFIG_OPTION_ID_TABLE', 100);
define('MOLAJO_CONFIG_OPTION_ID_FIELDS', 200);
define('MOLAJO_CONFIG_OPTION_ID_PUBLISH_FIELDS', 210);
define('MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS', 220);
define('MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES', 230);

/** State */
define('MOLAJO_CONFIG_OPTION_ID_STATE', 250);

define('MOLAJO_STATE_ARCHIVED', 2);
define('MOLAJO_STATE_PUBLISHED', 1);
define('MOLAJO_STATE_UNPUBLISHED', 0);
define('MOLAJO_STATE_TRASHED', -1);
define('MOLAJO_STATE_SPAMMED', -2);
define('MOLAJO_STATE_VERSION', -10);

/** User Interface */
define('MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS', 300);
define('MOLAJO_CONFIG_OPTION_ID_EDIT_TOOLBAR_BUTTONS', 310);
define('MOLAJO_CONFIG_OPTION_ID_TOOLBAR_SUBMENU_LINKS', 320);
define('MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER', 330);
define('MOLAJO_CONFIG_OPTION_ID_EDITOR_BUTTONS', 340);

/** MIME Types */
define('MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES', 400);
define('MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES', 410);
define('MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES', 420);
define('MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES', 430);

/** MVC */

/** Controller Tasks */
define('MOLAJO_CONFIG_OPTION_ID_TASK_TO_CONTROLLER', 1100);

/** Options */
define('MOLAJO_CONFIG_OPTION_ID_DEFAULT_OPTION', 1800);

/** Views */
define('MOLAJO_CONFIG_OPTION_ID_VIEWS', 2000);
define('MOLAJO_CONFIG_OPTION_ID_DEFAULT_VIEW', 2100);

/** View Layouts */
define('MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_LAYOUTS', 3000);
define('MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS', 3100);
define('MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_LAYOUTS', 3200);
define('MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS', 3300);

/** View Layout Formats */
define('MOLAJO_CONFIG_OPTION_ID_DISPLAY_VIEW_FORMATS', 4000);
define('MOLAJO_CONFIG_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS', 4100);
define('MOLAJO_CONFIG_OPTION_ID_EDIT_VIEW_FORMATS', 4200);
define('MOLAJO_CONFIG_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS', 4400);

/** Model */
define('MOLAJO_CONFIG_OPTION_ID_MODEL', 5000);

/** Plugin Helper */
define('MOLAJO_CONFIG_OPTION_ID_PLUGIN_TYPE', 6000);

/** ACL Component Information */
define('MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION', 10000);
define('MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS', 10100);
define('MOLAJO_CONFIG_OPTION_ID_TASKS_TO_ACL_METHODS', 10200);

/** ACL Groups */
define('MOLAJO_ACL_GROUP_PUBLIC', 1);
define('MOLAJO_ACL_GROUP_GUEST', 2);
define('MOLAJO_ACL_GROUP_REGISTERED', 3);
define('MOLAJO_ACL_GROUP_ADMINISTRATOR', 4);

/** ACL Actions */
define('MOLAJO_ACL_ACTION_LOGIN', 'login');
define('MOLAJO_ACL_ACTION_CREATE', 'create');
define('MOLAJO_ACL_ACTION_VIEW', 'view');
define('MOLAJO_ACL_ACTION_EDIT', 'edit');
define('MOLAJO_ACL_ACTION_PUBLISH', 'publish');
define('MOLAJO_ACL_ACTION_DELETE', 'delete');
define('MOLAJO_ACL_ACTION_ADMIN', 'admin');

/** Authentication */
define('MOLAJO_AUTHENTICATE_STATUS_SUCCESS', 1);
define('MOLAJO_AUTHENTICATE_STATUS_CANCEL', 2);
define('MOLAJO_AUTHENTICATE_STATUS_FAILURE', 4);

/** current url */
$currentURL = 'http';
if (isset($_SERVER['HTTPS'])) {
	$currentURL .= 's';
}
$currentURL .= '://';
if (isset($_SERVER['SERVER_NAME'])) {
	$currentURL .= $_SERVER['SERVER_NAME'];
}
if (isset($_SERVER['SERVER_PORT'])) {
    if ($_SERVER['SERVER_PORT'] == '80') {
    } else {
 	    $currentURL .= ":".$_SERVER['SERVER_PORT'];
    }
}
if (isset($_SERVER["REQUEST_URI"])) {
	$currentURL .= $_SERVER['REQUEST_URI'];
}
define('MOLAJO_CURRENT_URL', strtolower($currentURL));

if (strpos(MOLAJO_CURRENT_URL, MOLAJO_APPLICATION)) {
    define('MOLAJO_BASE_URL', substr(MOLAJO_CURRENT_URL, 0, strpos(MOLAJO_CURRENT_URL, MOLAJO_APPLICATION) - 1));
} else {
    define('MOLAJO_BASE_URL', MOLAJO_CURRENT_URL);
}

/** Detect the native operating system type */
$os = strtoupper(substr(PHP_OS, 0, 3));
if (defined('IS_WIN')) {
} else {
	define('IS_WIN', ($os === 'WIN') ? true : false);
}
if (defined('IS_MAC')) {
} else {
	define('IS_MAC', ($os === 'MAC') ? true : false);
}
if (defined('IS_UNIX')) {
} else {
	define('IS_UNIX', (($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
}

/** joomla */
define('_JEXEC', 1);
define('JPATH_BASE',		    MOLAJO_PATH_BASE);
define('JPATH_ROOT',		    MOLAJO_PATH_ROOT);
define('JPATH_CONFIGURATION',	MOLAJO_PATH_CONFIGURATION);
define('JPATH_LIBRARIES',		LIBRARIES);
define('JOOMLA_LIBRARY',		MOLAJO_PATH_ROOT.'/libraries/jplatform/joomla');
define('JPATH_SITE',			MOLAJO_PATH_SITE);
define('JPATH_ADMINISTRATOR',	MOLAJO_PATH_ADMINISTRATOR);
define('JPATH_PLUGINS',			MOLAJO_PATH_PLUGINS);
define('JPATH_INSTALLATION',	MOLAJO_PATH_INSTALLATION);
define('JPATH_CACHE',			MOLAJO_PATH_CACHE);
define('JPATH_MANIFESTS',		MOLAJO_PATH_MANIFESTS);
define('JPATH_THEMES',          MOLAJO_PATH_THEMES);
define('JPATH_PLATFORM',        LIBRARIES.'/jplatform');