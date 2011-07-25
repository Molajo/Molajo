<?php
/**
 * @version     $id: defines.php
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

define('MOLAJO_LIBRARY_FORM',       MOLAJO_LIBRARY.'/fields/form');
define('MOLAJO_LIBRARY_FIELDS',     MOLAJO_LIBRARY.'/fields/fields');
define('MOLAJO_LIBRARY_FIELDTYPES', MOLAJO_LIBRARY.'/fields/fieldtypes');
define('MOLAJO_LIBRARY_ATTRIBUTES', MOLAJO_LIBRARY.'/fields/attributes');

if (MOLAJO_APPLICATION == 'installation') {
    define('MOLAJO_PATH_THEMES', MOLAJO_PATH_BASE);
} else {
    define('MOLAJO_PATH_THEMES', MOLAJO_PATH_BASE.'/templates');
}

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
define('MOLAJO_ACL_GROUP_PUBLIC', 1);
define('MOLAJO_ACL_GROUP_GUEST', 2);
define('MOLAJO_ACL_GROUP_REGISTERED', 3);
define('MOLAJO_ACL_GROUP_ADMINISTRATOR', 4);
/** ACL Actions */
define('MOLAJO_ACL_ACTION_LOGIN', 'login');
define('MOLAJO_ACL_ACTION_CREATE', 'create');
define('MOLAJO_ACL_ACTION_VIEW', 'view');
define('MOLAJO_ACL_ACTION_EDIT', 'edit');
define('MOLAJO_ACL_ACTION_DELETE', 'delete');
define('MOLAJO_ACL_ACTION_ADMIN', 'admin');
/** MIME Types */
define('MOLAJO_CONFIG_OPTION_ID_AUDIO_MIMES', 1000);
define('MOLAJO_CONFIG_OPTION_ID_IMAGE_MIMES', 1010);
define('MOLAJO_CONFIG_OPTION_ID_TEXT_MIMES', 1020);
define('MOLAJO_CONFIG_OPTION_ID_VIDEO_MIMES ', 1030);

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

/** Layouts, Forms, and Parameters */
$temp = MOLAJO_PATH_ROOT.'/layouts';
define('MOLAJO_LAYOUTS', $temp);
$temp = MOLAJO_LAYOUTS.'/parameters';
define('MOLAJO_PARAMETERS', $temp);
$temp = MOLAJO_LAYOUTS.'/forms';
define('MOLAJO_FORM_LAYOUTS', $temp);

/** joomla */
define('_JEXEC', 1);
define('JPATH_BASE',		    MOLAJO_PATH_BASE);
define('JPATH_ROOT',		    MOLAJO_PATH_ROOT);
define('JPATH_CONFIGURATION',	MOLAJO_PATH_CONFIGURATION);
define('JPATH_LIBRARIES',		LIBRARIES);
define('JOOMLA_LIBRARY',		MOLAJO_PATH_ROOT.'/libraries/jplatform/joomla');
define('JPATH_SITE',			MOLAJO_PATH_SITE);
define('JPATH_ADMINISTRATOR',	MOLAJO_PATH_ADMINISTRATOR);
define('JPATH_PLATFORM',		MOLAJO_PATH_ROOT.'/libraries/jplatform');
define('JPATH_PLUGINS',			MOLAJO_PATH_PLUGINS);
define('JPATH_INSTALLATION',	MOLAJO_PATH_INSTALLATION);
define('JPATH_CACHE',			MOLAJO_PATH_CACHE);
define('JPATH_MANIFESTS',		MOLAJO_PATH_MANIFESTS);
define('JPATH_THEMES',          MOLAJO_PATH_THEMES);
/** overrides */
define('OVERRIDE', 1);
define('OVERRIDES_LIBRARY',		MOLAJO_PATH_ROOT.'/libraries/overrides');