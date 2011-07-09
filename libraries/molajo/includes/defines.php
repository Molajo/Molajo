<?php
/**
 * @version     $id: defines.php
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();
define('_JEXEC', 1);

define('OVERRIDE', 1);

define('JPATH_SITE',			JPATH_ROOT);
define('JPATH_ADMINISTRATOR',	JPATH_ROOT.'/administrator');
define('JPATH_PLATFORM',		JPATH_ROOT.'/libraries');
define('JPATH_PLUGINS',			JPATH_ROOT.'/plugins');
define('JPATH_INSTALLATION',	JPATH_ROOT.'/installation');
define('JPATH_CACHE',			JPATH_BASE.'/cache');
define('JPATH_MANIFESTS',		JPATH_ADMINISTRATOR.'/manifests');

if (MOLAJO_APPLICATION == 'installation') {
    define('JPATH_THEMES', JPATH_BASE);
} else {
    define('JPATH_THEMES', JPATH_BASE.'/templates');
}

/** JPATH_CONFIGURATION can be moved to hide the configuration.php file */
define('JPATH_CONFIGURATION',	JPATH_ROOT);

/** legacy  */
define('JPATH_LIBRARIES',		JPATH_ROOT.'/libraries');

/** overrides */
define('OVERRIDES_LIBRARY',		JPATH_ROOT.'/libraries/overrides');

/** joomla */
define('JOOMLA_LIBRARY',		JPATH_ROOT.'/libraries/joomla');

/** Configuration Fields **/
define('MOLAJO_CONFIG_OPTION_ID_FIELDS', 1);
define('MOLAJO_CONFIG_OPTION_ID_EDITSTATE_FIELDS', 2);
define('MOLAJO_CONFIG_OPTION_ID_JSON_FIELDS', 3);
/** Content Types **/
define('MOLAJO_CONFIG_OPTION_ID_CONTENT_TYPES', 10);
/** Views **/
define('MOLAJO_CONFIG_OPTION_ID_VIEW_PAIRS', 20);
/** Table **/
define('MOLAJO_CONFIG_OPTION_ID_TABLE', 45);
/** Layouts **/
define('MOLAJO_CONFIG_OPTION_ID_EDIT_LAYOUTS', 50);
define('MOLAJO_CONFIG_OPTION_ID_DEFAULT_LAYOUT', 60);
define('MOLAJO_CONFIG_OPTION_ID_LAYOUTS', 70);
/** Formats **/
define('MOLAJO_CONFIG_OPTION_ID_FORMAT', 75);
/** Tasks **/
define('MOLAJO_CONFIG_OPTION_ID_DISPLAY_CONTROLLER_TASKS', 80);
define('MOLAJO_CONFIG_OPTION_ID_SINGLE_CONTROLLER_TASKS', 85);
define('MOLAJO_CONFIG_OPTION_ID_MULTIPLE_CONTROLLER_TASKS', 90);
/** Tasks and ACL Methods **/
define('MOLAJO_CONFIG_OPTION_ID_TASK_ACL_METHODS', 100);
define('MOLAJO_CONFIG_OPTION_ID_ACL_IMPLEMENTATION', 110);
define('MOLAJO_CONFIG_OPTION_ID_ACL_ITEM_TESTS', 120);
/** Toolbar, Submenu, Filters **/
define('MOLAJO_CONFIG_OPTION_ID_LIST_TOOLBAR_BUTTONS', 200);
define('MOLAJO_CONFIG_OPTION_ID_EDIT_TOOLBAR_BUTTONS', 210);
define('MOLAJO_CONFIG_OPTION_ID_TOOLBAR_SUBMENU_LINKS', 220);
define('MOLAJO_CONFIG_OPTION_ID_LISTBOX_FILTER', 230);
/** Editor Buttons **/
define('MOLAJO_CONFIG_OPTION_ID_EDIT_BUTTONS', 240);
/** State Properties **/
define('MOLAJO_CONFIG_OPTION_ID_STATE', 250);
/** State **/
define('MOLAJO_STATE_ARCHIVED', 2);
define('MOLAJO_STATE_PUBLISHED', 1);
define('MOLAJO_STATE_UNPUBLISHED', 0);
define('MOLAJO_STATE_TRASHED', -1);
define('MOLAJO_STATE_SPAMMED', -2);
define('MOLAJO_STATE_VERSION', -10);
/** ACL **/
define('MOLAJO_ACL_GROUP_ADMINISTRATOR', 1);
define('MOLAJO_ACL_GROUP_REGISTERED', 2);
define('MOLAJO_ACL_GROUP_GUEST', 3);
define('MOLAJO_ACL_GROUP_PUBLIC', 4);
/** ACL Actions */
define('MOLAJO_ACL_ACTION_LOGIN', 'login');
define('MOLAJO_ACL_ACTION_CREATE', 'create');
define('MOLAJO_ACL_ACTION_VIEW', 'view');
define('MOLAJO_ACL_ACTION_EDIT', 'edit');
define('MOLAJO_ACL_ACTION_DELETE', 'delete');
define('MOLAJO_ACL_ACTION_ADMIN', 'admin');

/** Layouts **/
define('MOLAJO_CONFIG_OPTION_ID_PARAMETERS_LAYOUTS', 500);

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

/** Layouts: Drivers, Layouts, and Parameters */
$temp = JPATH_ROOT.'/layouts';
define('MOLAJO_LAYOUTS', $temp);
$temp = MOLAJO_LIBRARY.'/parameters';
define('MOLAJO_PARAMETERS', $temp);