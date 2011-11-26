<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (defined('MOLAJO_SITE_PATH')) { } else { define('MOLAJO_SITE_PATH', MOLAJO_BASE_FOLDER.'/sites/'.MOLAJO_SITE); }

if (defined('MOLAJO_APPLICATIONS_PATH')) { } else { define('MOLAJO_APPLICATIONS_PATH', MOLAJO_BASE_FOLDER.'/applications'); }

if (defined('MOLAJO_EXTENSIONS')) { } else { define('MOLAJO_EXTENSIONS', MOLAJO_BASE_FOLDER.'/extensions'); }
if (defined('MOLAJO_EXTENSION_COMPONENTS')) { } else { define('MOLAJO_EXTENSION_COMPONENTS', MOLAJO_EXTENSIONS.'/components'); }
if (defined('MOLAJO_EXTENSION_LANGUAGES')) { } else { define('MOLAJO_EXTENSION_LANGUAGES', MOLAJO_EXTENSIONS.'/language'); }
if (defined('MOLAJO_EXTENSION_LAYOUTS')) { } else { define('MOLAJO_EXTENSION_LAYOUTS', MOLAJO_EXTENSIONS.'/layouts'); }
if (defined('MOLAJO_EXTENSION_MANIFESTS')) { } else { define('MOLAJO_EXTENSION_MANIFESTS', MOLAJO_EXTENSIONS.'/manifests'); }
if (defined('MOLAJO_EXTENSION_MODULES')) { } else { define('MOLAJO_EXTENSION_MODULES', MOLAJO_EXTENSIONS.'/modules'); }
if (defined('MOLAJO_EXTENSION_PARAMETERS')) { } else { define('MOLAJO_EXTENSION_PARAMETERS', MOLAJO_EXTENSIONS.'/parameters'); }
if (defined('MOLAJO_EXTENSION_PLUGINS')) { } else { define('MOLAJO_EXTENSION_PLUGINS', MOLAJO_EXTENSIONS.'/plugins'); }
if (defined('MOLAJO_EXTENSION_TEMPLATES')) { } else { define('MOLAJO_EXTENSION_TEMPLATES', MOLAJO_EXTENSIONS.'/templates'); }
if (defined('JPATH_COMPONENT')) { } else { define('JPATH_COMPONENT', MOLAJO_EXTENSION_COMPONENTS); }

if (defined('MOLAJO_LIBRARY')) { } else { define('MOLAJO_LIBRARY', LIBRARIES.'molajo'); }
if (defined('MOLAJO_LIBRARY_DATA')) { } else { define('MOLAJO_LIBRARY_DATA', MOLAJO_LIBRARY.'/data'); }
if (defined('MOLAJO_LIBRARY_MVC')) { } else { define('MOLAJO_LIBRARY_MVC', MOLAJO_LIBRARY.'/mvc'); }

/** status values */
define('MOLAJO_STATUS_ARCHIVED', 2);
define('MOLAJO_STATUS_PUBLISHED', 1);
define('MOLAJO_STATUS_UNPUBLISHED', 0);
define('MOLAJO_STATUS_TRASHED', -1);
define('MOLAJO_STATUS_SPAMMED', -2);
define('MOLAJO_STATUS_DRAFT', -5);
define('MOLAJO_STATUS_VERSION', -10);

/** Asset Types */
define('MOLAJO_ASSET_TYPE_BASE_BEGIN', 0);
define('MOLAJO_ASSET_TYPE_BASE_CORE', 1);
define('MOLAJO_ASSET_TYPE_BASE_SITE', 10);
define('MOLAJO_ASSET_TYPE_BASE_APPLICATION', 50);
define('MOLAJO_ASSET_TYPE_BASE_END', 99);

define('MOLAJO_ASSET_TYPE_GROUP_BEGIN', 100);
define('MOLAJO_ASSET_TYPE_GROUP_SYSTEM', 100);
define('MOLAJO_ASSET_TYPE_GROUP_NORMAL', 110);
define('MOLAJO_ASSET_TYPE_GROUP_USER', 120);
define('MOLAJO_ASSET_TYPE_GROUP_END', 199);

define('MOLAJO_ASSET_TYPE_USER_BEGIN', 500);
define('MOLAJO_ASSET_TYPE_USER', 500);
define('MOLAJO_ASSET_TYPE_USER_END', 599);

define('MOLAJO_ASSET_TYPE_EXTENSION_BEGIN', 1000);
define('MOLAJO_ASSET_TYPE_EXTENSION_CORE', 1000);
define('MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT', 1050);
define('MOLAJO_ASSET_TYPE_EXTENSION_LANGUAGE', 1100);
define('MOLAJO_ASSET_TYPE_EXTENSION_LAYOUT', 1150);
define('MOLAJO_ASSET_TYPE_EXTENSION_LIBRARY', 1200);
define('MOLAJO_ASSET_TYPE_EXTENSION_MANIFEST', 1250);
define('MOLAJO_ASSET_TYPE_EXTENSION_MENU', 1300);
define('MOLAJO_ASSET_TYPE_EXTENSION_MODULE', 1350);
define('MOLAJO_ASSET_TYPE_EXTENSION_PARAMETER', 1400);
define('MOLAJO_ASSET_TYPE_EXTENSION_PLUGIN', 1450);
define('MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE', 1500);
define('MOLAJO_ASSET_TYPE_EXTENSION_END', 1999);

define('MOLAJO_ASSET_TYPE_MENU_ITEM_BEGIN', 2000);
define('MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT', 2000);
define('MOLAJO_ASSET_TYPE_MENU_ITEM_LINK', 2100);
define('MOLAJO_ASSET_TYPE_MENU_ITEM_MODULE', 2200);
define('MOLAJO_ASSET_TYPE_MENU_ITEM_SEPARATOR', 2300);
define('MOLAJO_ASSET_TYPE_MENU_ITEM_END', 2999);

define('MOLAJO_ASSET_TYPE_CATEGORY_BEGIN', 3000);
define('MOLAJO_ASSET_TYPE_CATEGORY_LIST', 3000);
define('MOLAJO_ASSET_TYPE_CATEGORY_TAG', 3500);
define('MOLAJO_ASSET_TYPE_CATEGORY_END', 3999);

define('MOLAJO_ASSET_TYPE_CONTENT_BEGIN', 10000);
define('MOLAJO_ASSET_TYPE_CONTENT_ARTICLE', 10000);
define('MOLAJO_ASSET_TYPE_CONTENT_CONTACT', 20000);
define('MOLAJO_ASSET_TYPE_CONTENT_COMMENT', 30000);
define('MOLAJO_ASSET_TYPE_CONTENT_MEDIA', 40000);
define('MOLAJO_ASSET_TYPE_CONTENT_LAYOUT', 50000);
define('MOLAJO_ASSET_TYPE_CONTENT_END', 999999);

/** System Groups */
define('MOLAJO_SYSTEM_GROUP_PUBLIC', 1);
define('MOLAJO_SYSTEM_GROUP_GUEST', 2);
define('MOLAJO_SYSTEM_GROUP_REGISTERED', 3);
define('MOLAJO_SYSTEM_GROUP_ADMINISTRATOR', 4);

/** Action Types */
define('MOLAO_ACTION_TYPE_LOGIN', 'login');
define('MOLAO_ACTION_TYPE_CREATE', 'create');
define('MOLAO_ACTION_TYPE_VIEW', 'view');
define('MOLAO_ACTION_TYPE_EDIT', 'edit');
define('MOLAO_ACTION_TYPE_PUBLISH', 'publish');
define('MOLAO_ACTION_TYPE_DELETE', 'delete');
define('MOLAO_ACTION_TYPE_ADMIN', 'admin');

/** Authentication */
define('MOLAJO_AUTHENTICATE_STATUS_SUCCESS', 1);
define('MOLAJO_AUTHENTICATE_STATUS_CANCEL', 2);
define('MOLAJO_AUTHENTICATE_STATUS_FAILURE', 4);

/** ACL Component Information */
define('MOLAJO_EXTENSION_OPTION_ID_ACL_IMPLEMENTATION', 10000);
define('MOLAJO_EXTENSION_OPTION_ID_ACL_ITEM_TESTS', 10100);
define('MOLAJO_EXTENSION_OPTION_ID_ACL_TASK_TO_METHODS', 10200);

/** Table */
define('MOLAJO_EXTENSION_OPTION_ID_TABLE', 100);
define('MOLAJO_EXTENSION_OPTION_ID_FIELDS', 200);
define('MOLAJO_EXTENSION_OPTION_ID_PUBLISH_FIELDS', 210);
define('MOLAJO_EXTENSION_OPTION_ID_JSON_FIELDS', 220);

/** State */
define('MOLAJO_EXTENSION_OPTION_ID_STATUS', 250);

/** User Interface */
define('MOLAJO_EXTENSION_OPTION_ID_LIST_TOOLBAR_BUTTONS', 300);
define('MOLAJO_EXTENSION_OPTION_ID_EDIT_TOOLBAR_BUTTONS', 310);
define('MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_SUBMENU_LINKS', 320);
define('MOLAJO_EXTENSION_OPTION_ID_LISTBOX_FILTERS', 330);
define('MOLAJO_EXTENSION_OPTION_ID_EDITOR_BUTTONS', 340);
define('MOLAJO_EXTENSION_OPTION_ID_AUDIO_MIMES', 400);
define('MOLAJO_EXTENSION_OPTION_ID_IMAGE_MIMES', 410);
define('MOLAJO_EXTENSION_OPTION_ID_TEXT_MIMES', 420);
define('MOLAJO_EXTENSION_OPTION_ID_VIDEO_MIMES', 430);

define('MOLAJO_EXTENSION_OPTION_ID_TASK_TO_CONTROLLER', 1100);
define('MOLAJO_EXTENSION_OPTION_ID_DEFAULT_OPTION', 1800);
define('MOLAJO_EXTENSION_OPTION_ID_VIEWS', 2000);
define('MOLAJO_EXTENSION_OPTION_ID_DEFAULT_VIEW', 2100);
define('MOLAJO_EXTENSION_OPTION_ID_DISPLAY_VIEW_LAYOUTS', 3000);
define('MOLAJO_EXTENSION_OPTION_ID_DEFAULT_DISPLAY_VIEW_LAYOUTS', 3100);
define('MOLAJO_EXTENSION_OPTION_ID_EDIT_VIEW_LAYOUTS', 3200);
define('MOLAJO_EXTENSION_OPTION_ID_DEFAULT_EDIT_VIEW_LAYOUTS', 3300);
define('MOLAJO_EXTENSION_OPTION_ID_DISPLAY_VIEW_FORMATS', 4000);
define('MOLAJO_EXTENSION_OPTION_ID_DEFAULT_DISPLAY_VIEW_FORMATS', 4100);
define('MOLAJO_EXTENSION_OPTION_ID_EDIT_VIEW_FORMATS', 4200);
define('MOLAJO_EXTENSION_OPTION_ID_DEFAULT_EDIT_VIEW_FORMATS', 4400);
/** Model */
define('MOLAJO_EXTENSION_OPTION_ID_MODEL', 5000);
/** Plugin Type */
define('MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE', 6000);

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


