<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Allows for quoting in language .ini files.
 */
define('_QQ_', '"');

/**
 *  Platform
 */
if (defined('PLATFORMS')) {
} else {
    define('PLATFORMS', MOLAJO_BASE_FOLDER . '/platforms');
}

if (defined('PLATFORM_MOLAJO')) {
} else {
    define('PLATFORM_MOLAJO', PLATFORMS . '/molajo');
}
if (defined('PLATFORM_DOCTRINE')) {
} else {
    define('PLATFORM_DOCTRINE', PLATFORMS . '/Doctrine');
}
if (defined('PLATFORM_DOCTRINE_EXTENSIONS')) {
} else {
    define('PLATFORM_DOCTRINE_EXTENSIONS', PLATFORMS . '/DoctrineExtensions');
}
if (defined('PLATFORM_TWIG')) {
} else {
    define('PLATFORM_TWIG', PLATFORMS . '/Twig');
}
if (defined('PLATFORM_MUSTACHE')) {
} else {
    define('PLATFORM_MUSTACHE', PLATFORMS . '/Mustache');
}

/**
 *  Applications
 */
if (defined('MOLAJO_APPLICATIONS_MVC')) {
} else {
    define('MOLAJO_APPLICATIONS_MVC', MOLAJO_APPLICATIONS_CORE . '/mvc');
}
if (defined('MOLAJO_APPLICATIONS_MVC_URL')) {
} else {
    define('MOLAJO_APPLICATIONS_MVC_URL', MOLAJO_BASE_URL . 'applications/mvc');
}
if (defined('MOLAJO_APPLICATIONS_CORE_DATA')) {
} else {
    define('MOLAJO_APPLICATIONS_CORE_DATA', MOLAJO_APPLICATIONS_CORE . '/data');
}
if (defined('MOLAJO_APPLICATIONS_CORE_DATA_ENTITIES')) {
} else {
    define('MOLAJO_APPLICATIONS_CORE_DATA_ENTITIES', MOLAJO_APPLICATIONS_CORE_DATA . '/entities');
}
if (defined('MOLAJO_APPLICATIONS_CORE_DATA_PROXIES')) {
} else {
    define('MOLAJO_APPLICATIONS_CORE_DATA_PROXIES', MOLAJO_APPLICATIONS_CORE_DATA . '/proxies');
}

/**
 *  Extensions
 */
if (defined('MOLAJO_EXTENSIONS')) {
} else {
    define('MOLAJO_EXTENSIONS', MOLAJO_BASE_FOLDER . '/extensions');
}
if (defined('MOLAJO_EXTENSIONS_COMPONENTS')) {
} else {
    define('MOLAJO_EXTENSIONS_COMPONENTS', MOLAJO_EXTENSIONS . '/components');
}
if (defined('MOLAJO_EXTENSIONS_LANGUAGES')) {
} else {
    define('MOLAJO_EXTENSIONS_LANGUAGES', MOLAJO_EXTENSIONS . '/languages');
}
if (defined('MOLAJO_EXTENSIONS_VIEWS')) {
} else {
    define('MOLAJO_EXTENSIONS_VIEWS', MOLAJO_EXTENSIONS . '/views');
}
if (defined('MOLAJO_EXTENSIONS_MANIFESTS')) {
} else {
    define('MOLAJO_EXTENSIONS_MANIFESTS', MOLAJO_EXTENSIONS . '/manifests');
}
if (defined('MOLAJO_EXTENSIONS_MODULES')) {
} else {
    define('MOLAJO_EXTENSIONS_MODULES', MOLAJO_EXTENSIONS . '/modules');
}
if (defined('MOLAJO_EXTENSIONS_PLUGINS')) {
} else {
    define('MOLAJO_EXTENSIONS_PLUGINS', MOLAJO_EXTENSIONS . '/plugins');
}
if (defined('MOLAJO_EXTENSIONS_TEMPLATES')) {
} else {
    define('MOLAJO_EXTENSIONS_TEMPLATES', MOLAJO_EXTENSIONS . '/templates');
}

if (defined('MOLAJO_EXTENSIONS_COMPONENTS_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_COMPONENTS_URL', MOLAJO_BASE_URL . 'extensions/components');
}
if (defined('MOLAJO_EXTENSIONS_VIEWS_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_VIEWS_URL', MOLAJO_BASE_URL . 'extensions/views');
}
if (defined('MOLAJO_EXTENSIONS_MODULES_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_MODULES_URL', MOLAJO_BASE_URL . 'extensions/modules');
}
if (defined('MOLAJO_EXTENSIONS_PLUGINS_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_PLUGINS_URL', MOLAJO_BASE_URL . 'extensions/plugins');
}
if (defined('MOLAJO_EXTENSIONS_TEMPLATES_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_TEMPLATES_URL', MOLAJO_BASE_URL . 'extensions/templates');
}
/**
 *  ACTION TYPES
 */
define('MOLAO_ACTION_TYPE_LOGIN', 'login');
define('MOLAO_ACTION_TYPE_CREATE', 'create');
define('MOLAO_ACTION_TYPE_VIEW', 'view');
define('MOLAO_ACTION_TYPE_EDIT', 'edit');
define('MOLAO_ACTION_TYPE_PUBLISH', 'publish');
define('MOLAO_ACTION_TYPE_DELETE', 'delete');
define('MOLAO_ACTION_TYPE_ADMIN', 'administer');

/**
 *  ASSET TYPES
 */
define('MOLAJO_ASSET_TYPE_BASE_BEGIN', 0);
define('MOLAJO_ASSET_TYPE_BASE_CORE', 1);
define('MOLAJO_ASSET_TYPE_BASE_SITE', 10);
define('MOLAJO_ASSET_TYPE_BASE_APPLICATION', 50);
define('MOLAJO_ASSET_TYPE_BASE_END', 99);

define('MOLAJO_ASSET_TYPE_GROUP_BEGIN', 100);
define('MOLAJO_ASSET_TYPE_GROUP_SYSTEM', 100);
define('MOLAJO_ASSET_TYPE_GROUP_NORMAL', 110);
define('MOLAJO_ASSET_TYPE_GROUP_USER', 120);
define('MOLAJO_ASSET_TYPE_GROUP_FRIEND', 130);
define('MOLAJO_ASSET_TYPE_GROUP_END', 199);

define('MOLAJO_ASSET_TYPE_USER_BEGIN', 500);
define('MOLAJO_ASSET_TYPE_USER', 500);
define('MOLAJO_ASSET_TYPE_USER_END', 599);

define('MOLAJO_ASSET_TYPE_EXTENSION_BEGIN', 1000);
define('MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT', 1050);
define('MOLAJO_ASSET_TYPE_EXTENSION_LANGUAGE', 1100);
define('MOLAJO_ASSET_TYPE_EXTENSION_VIEW', 1150);
define('MOLAJO_ASSET_TYPE_EXTENSION_MENU', 1300);
define('MOLAJO_ASSET_TYPE_EXTENSION_MODULE', 1350);
define('MOLAJO_ASSET_TYPE_EXTENSION_POSITION', 1351);
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
define('MOLAJO_ASSET_TYPE_CONTENT_VIEW', 50000);
define('MOLAJO_ASSET_TYPE_CONTENT_END', 999999);

/**
 *  AUTHENTICATION
 */
define('MOLAJO_AUTHENTICATE_STATUS_SUCCESS', 1);
define('MOLAJO_AUTHENTICATE_STATUS_CANCEL', 2);
define('MOLAJO_AUTHENTICATE_STATUS_FAILURE', 4);

/**
 *  SYSTEM GROUPS
 */
define('MOLAJO_SYSTEM_GROUP_PUBLIC', 1);
define('MOLAJO_SYSTEM_GROUP_GUEST', 2);
define('MOLAJO_SYSTEM_GROUP_REGISTERED', 3);
define('MOLAJO_SYSTEM_GROUP_ADMINISTRATOR', 4);

/**
 *  STATUS
 */
define('MOLAJO_STATUS_ARCHIVED', 2);
define('MOLAJO_STATUS_PUBLISHED', 1);
define('MOLAJO_STATUS_UNPUBLISHED', 0);
define('MOLAJO_STATUS_TRASHED', -1);
define('MOLAJO_STATUS_SPAMMED', -2);
define('MOLAJO_STATUS_DRAFT', -5);
define('MOLAJO_STATUS_VERSION', -10);

/**
 *  MESSAGE TYPES
 */
define('MOLAJO_MESSAGE_TYPE_MESSAGE', 'message');
define('MOLAJO_MESSAGE_TYPE_NOTICE', 'notice');
define('MOLAJO_MESSAGE_TYPE_WARNING', 'warning');
define('MOLAJO_MESSAGE_TYPE_ERROR', 'error');

/**
 *  EXTENSION OPTIONS
 */
define('MOLAJO_EXTENSION_OPTION_ID_TABLE', 100);
define('MOLAJO_EXTENSION_OPTION_ID_FIELDS', 200);
define('MOLAJO_EXTENSION_OPTION_ID_DISPLAY_ONLY_FIELDS', 205);
define('MOLAJO_EXTENSION_OPTION_ID_PUBLISH_FIELDS', 210);
define('MOLAJO_EXTENSION_OPTION_ID_JSON_FIELDS', 220);

/** Status */
define('MOLAJO_EXTENSION_OPTION_ID_STATUS', 250);

/** User Interface */
define('MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_LIST', 300);
define('MOLAJO_EXTENSION_OPTION_ID_SUBMENU_LIST', 310);
define('MOLAJO_EXTENSION_OPTION_ID_FILTERS_LIST', 320);
define('MOLAJO_EXTENSION_OPTION_ID_TOOLBAR_EDIT', 330);
define('MOLAJO_EXTENSION_OPTION_ID_EDITOR_BUTTONS', 340);

define('MOLAJO_EXTENSION_OPTION_ID_MIMES_AUDIO', 400);
define('MOLAJO_EXTENSION_OPTION_ID_MIMES_IMAGE', 410);
define('MOLAJO_EXTENSION_OPTION_ID_MIMES_TEXT', 420);
define('MOLAJO_EXTENSION_OPTION_ID_MIMES_VIDEO', 430);

/** Plugin Type */
define('MOLAJO_EXTENSION_OPTION_ID_PLUGIN_TYPE', 6000);

/** ACL Component Information */
define('MOLAJO_EXTENSION_OPTION_ID_ACL_ITEM_TESTS', 10100);
define('MOLAJO_EXTENSION_OPTION_ID_ACL_TASK_TO_METHODS', 10200);

/**
 *  Rendering
 */
define('MOLAJO_STOP_LOOP', 100);

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
