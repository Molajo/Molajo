<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (defined('MOLAJO_APPLICATIONS_PATH')) { } else { define('MOLAJO_APPLICATIONS_PATH', MOLAJO_BASE_FOLDER.'/applications'); }

if (defined('MOLAJO_SITE_CACHE')) { } else { define('MOLAJO_SITE_CACHE', MOLAJO_SITE.'/cache'); }
if (defined('MOLAJO_SITE_IMAGES')) { } else { define('MOLAJO_SITE_IMAGES', MOLAJO_SITE.'/images'); }
if (defined('MOLAJO_SITE_LOGS')) { } else { define('MOLAJO_SITE_LOGS', MOLAJO_SITE.'/logs'); }
if (defined('MOLAJO_SITE_MEDIA')) { } else { define('MOLAJO_SITE_MEDIA', MOLAJO_SITE.'/media'); }
if (defined('MOLAJO_SITE_TMP')) { } else { define('MOLAJO_SITE_TMP', MOLAJO_SITE.'/tmp'); }

if (defined('MOLAJO_EXTENSIONS')) { } else { define('MOLAJO_EXTENSIONS', MOLAJO_BASE_FOLDER.'/extensions'); }
if (defined('MOLAJO_EXTENSION_COMPONENTS')) { } else { define('MOLAJO_EXTENSION_COMPONENTS', MOLAJO_EXTENSIONS.'/components'); }
if (defined('MOLAJO_EXTENSION_LANGUAGES')) { } else { define('MOLAJO_EXTENSION_LANGUAGES', MOLAJO_EXTENSIONS.'/languages'); }
if (defined('MOLAJO_EXTENSION_LAYOUTS')) { } else { define('MOLAJO_EXTENSION_LAYOUTS', MOLAJO_EXTENSIONS.'/layouts'); }
if (defined('MOLAJO_EXTENSION_LAYOUT_COMMON')) { } else { define('MOLAJO_EXTENSION_LAYOUT_COMMON', MOLAJO_EXTENSION_LAYOUTS.'/common'); }
if (defined('MOLAJO_EXTENSION_LAYOUT_DOCUMENT')) { } else { define('MOLAJO_EXTENSION_LAYOUT_DOCUMENT', MOLAJO_EXTENSION_LAYOUTS.'/document'); }
if (defined('MOLAJO_EXTENSION_LAYOUT_EXTENSIONS')) { } else { define('MOLAJO_EXTENSION_LAYOUT_EXTENSIONS', MOLAJO_EXTENSION_LAYOUTS.'/extensions'); }
if (defined('MOLAJO_EXTENSION_LAYOUT_FORMFIELDS')) { } else { define('MOLAJO_EXTENSION_LAYOUT_FORMFIELDS', MOLAJO_EXTENSION_LAYOUTS.'/formfields'); }
if (defined('MOLAJO_EXTENSION_LAYOUT_WRAPS')) { } else { define('MOLAJO_EXTENSION_LAYOUT_WRAPS', MOLAJO_EXTENSION_LAYOUTS.'/wraps'); }
if (defined('MOLAJO_EXTENSION_MANIFESTS')) { } else { define('MOLAJO_EXTENSION_MANIFESTS', MOLAJO_EXTENSIONS.'/manifests'); }
if (defined('MOLAJO_EXTENSION_MODULES')) { } else { define('MOLAJO_EXTENSION_MODULES', MOLAJO_EXTENSIONS.'/modules'); }
if (defined('MOLAJO_EXTENSION_PARAMETERS')) { } else { define('MOLAJO_EXTENSION_PARAMETERS', MOLAJO_EXTENSIONS.'/parameters'); }
if (defined('MOLAJO_EXTENSION_PLUGINS')) { } else { define('MOLAJO_EXTENSION_PLUGINS', MOLAJO_EXTENSIONS.'/plugins'); }
if (defined('MOLAJO_EXTENSION_TEMPLATES')) { } else { define('MOLAJO_EXTENSION_TEMPLATES', MOLAJO_EXTENSIONS.'/templates'); }

if (defined('MOLAJO_LIBRARY')) { } else { define('MOLAJO_LIBRARY', LIBRARIES.'/molajo'); }
if (defined('MOLAJO_LIBRARY_COMPONENT')) { } else { define('MOLAJO_LIBRARY_COMPONENT', MOLAJO_LIBRARY.'/component'); }
if (defined('MOLAJO_LIBRARY_CONTROLLERS')) { } else { define('MOLAJO_LIBRARY_CONTROLLERS', MOLAJO_LIBRARY_COMPONENT.'/controllers'); }
if (defined('MOLAJO_LIBRARY_FIELDS')) { } else { define('MOLAJO_LIBRARY_FIELDS', MOLAJO_LIBRARY_COMPONENT.'/fields'); }
if (defined('MOLAJO_LIBRARY_MODELS')) { } else { define('MOLAJO_LIBRARY_MODELS', MOLAJO_LIBRARY_COMPONENT.'/models'); }
if (defined('MOLAJO_LIBRARY_ROUTER')) { } else { define('MOLAJO_LIBRARY_ROUTER', MOLAJO_LIBRARY_COMPONENT.'/router'); }
if (defined('MOLAJO_LIBRARY_TABLES')) { } else { define('MOLAJO_LIBRARY_TABLES', MOLAJO_LIBRARY_COMPONENT.'/tables'); }
if (defined('MOLAJO_LIBRARY_VIEWS')) { } else { define('MOLAJO_LIBRARY_VIEWS', MOLAJO_LIBRARY_COMPONENT.'/views'); }
if (defined('MOLAJO_LIBRARY_ATTRIBUTES')) { } else { define('MOLAJO_LIBRARY_ATTRIBUTES', MOLAJO_LIBRARY_COMPONENT.'/attributes'); }

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
define('MOLAJO_CONFIG_OPTION_ID_ACL_TASK_TO_METHODS', 10200);

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

