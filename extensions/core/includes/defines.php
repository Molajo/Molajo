<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Rendering
 */
if (defined('MOLAJO_STOP_LOOP')) {
} else {
    define('MOLAJO_STOP_LOOP', 100);
}

/**
 *  Allows for quoting in language .ini files.
 */
if (defined('_QQ_')) {
} else {
    define('_QQ_', '"');
}

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
if (defined('MOLAJO_DOCTRINE_MODELS')) {
} else {
    define('MOLAJO_DOCTRINE_MODELS', MOLAJO_APPLICATIONS_MVC . '/models');
}
if (defined('MOLAJO_DOCTRINE_PROXIES')) {
} else {
    define('MOLAJO_DOCTRINE_PROXIES', MOLAJO_APPLICATIONS_MVC . '/models');
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
if (defined('MOLAJO_EXTENSIONS_FORMFIELDS')) {
} else {
    define('MOLAJO_EXTENSIONS_FORMFIELDS', MOLAJO_EXTENSIONS . '/formfields');
}
if (defined('MOLAJO_EXTENSIONS_LANGUAGES')) {
} else {
    define('MOLAJO_EXTENSIONS_LANGUAGES', MOLAJO_EXTENSIONS . '/languages');
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
if (defined('MOLAJO_EXTENSIONS_THEMES')) {
} else {
    define('MOLAJO_EXTENSIONS_THEMES', MOLAJO_EXTENSIONS . '/themes');
}
if (defined('MOLAJO_EXTENSIONS_VIEWS')) {
} else {
    define('MOLAJO_EXTENSIONS_VIEWS', MOLAJO_EXTENSIONS . '/views');
}

if (defined('MOLAJO_EXTENSIONS_COMPONENTS_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_COMPONENTS_URL', MOLAJO_BASE_URL . 'extensions/components');
}
if (defined('MOLAJO_EXTENSIONS_FORMFIELDS_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_FORMFIELDS_URL', MOLAJO_BASE_URL . 'extensions/formfields');
}
if (defined('MOLAJO_EXTENSIONS_MODULES_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_MODULES_URL', MOLAJO_BASE_URL . 'extensions/modules');
}
if (defined('MOLAJO_EXTENSIONS_PLUGINS_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_PLUGINS_URL', MOLAJO_BASE_URL . 'extensions/plugins');
}
if (defined('MOLAJO_EXTENSIONS_THEMES_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_THEMES_URL', MOLAJO_BASE_URL . 'extensions/themes');
}
if (defined('MOLAJO_EXTENSIONS_VIEWS_URL')) {
} else {
    define('MOLAJO_EXTENSIONS_VIEWS_URL', MOLAJO_BASE_URL . 'extensions/views');
}
/**
 *  ACTION TYPES
 */
if (defined('MOLAO_ACTION_TYPE_LOGIN')) {
} else {
    define('MOLAO_ACTION_TYPE_LOGIN', 'login');
}
if (defined('MOLAO_ACTION_TYPE_CREATE')) {
} else {
    define('MOLAO_ACTION_TYPE_CREATE', 'create');
}
if (defined('MOLAO_ACTION_TYPE_VIEW')) {
} else {
    define('MOLAO_ACTION_TYPE_VIEW', 'view');
}
if (defined('MOLAO_ACTION_TYPE_EDIT')) {
} else {
    define('MOLAO_ACTION_TYPE_EDIT', 'edit');
}
if (defined('MOLAO_ACTION_TYPE_PUBLISH')) {
} else {
    define('MOLAO_ACTION_TYPE_PUBLISH', 'publish');
}
if (defined('MOLAO_ACTION_TYPE_DELETE')) {
} else {
    define('MOLAO_ACTION_TYPE_DELETE', 'delete');
}
if (defined('MOLAO_ACTION_TYPE_ADMIN')) {
} else {
    define('MOLAO_ACTION_TYPE_ADMIN', 'administer');
}

/**
 *  ASSET TYPES
 */
/** base */
if (defined('MOLAJO_ASSET_TYPE_BASE_BEGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_BASE_BEGIN', 0);
}
if (defined('MOLAJO_ASSET_TYPE_BASE_CORE')) {
} else {
    define('MOLAJO_ASSET_TYPE_BASE_CORE', 1);
}
if (defined('MOLAJO_ASSET_TYPE_BASE_SITE')) {
} else {
    define('MOLAJO_ASSET_TYPE_BASE_SITE', 10);
}
if (defined('MOLAJO_ASSET_TYPE_BASE_APPLICATION')) {
} else {
    define('MOLAJO_ASSET_TYPE_BASE_APPLICATION', 50);
}
if (defined('MOLAJO_ASSET_TYPE_BASE_END')) {
} else {
    define('MOLAJO_ASSET_TYPE_BASE_END', 99);
}

/** group */
if (defined('MOLAJO_ASSET_TYPE_GROUP_BEGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_GROUP_BEGIN', 100);
}
if (defined('MOLAJO_ASSET_TYPE_GROUP_SYSTEM')) {
} else {
    define('MOLAJO_ASSET_TYPE_GROUP_SYSTEM', 100);
}
if (defined('MOLAJO_ASSET_TYPE_GROUP_NORMAL')) {
} else {
    define('MOLAJO_ASSET_TYPE_GROUP_NORMAL', 110);
}
if (defined('MOLAJO_ASSET_TYPE_GROUP_USER')) {
} else {
    define('MOLAJO_ASSET_TYPE_GROUP_USER', 120);
}
if (defined('MOLAJO_ASSET_TYPE_GROUP_FRIEND')) {
} else {
    define('MOLAJO_ASSET_TYPE_GROUP_FRIEND', 130);
}
if (defined('MOLAJO_ASSET_TYPE_GROUP_END')) {
} else {
    define('MOLAJO_ASSET_TYPE_GROUP_END', 199);
}

/** user */
if (defined('MOLAJO_ASSET_TYPE_USER_BEGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_USER_BEGIN', 500);
}
if (defined('MOLAJO_ASSET_TYPE_USER')) {
} else {
    define('MOLAJO_ASSET_TYPE_USER', 500);
}
if (defined('MOLAJO_ASSET_TYPE_USER_END')) {
} else {
    define('MOLAJO_ASSET_TYPE_USER_END', 599);
}

/** extension */
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_BEGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_BEGIN', 1000);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_COMPONENT', 1050);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_LANGUAGE')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_LANGUAGE', 1100);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_PAGE_VIEW', 1150);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_TEMPLATE_VIEW', 1200);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_WRAP_VIEW', 1250);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_MENU')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_MENU', 1300);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_MODULE')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_MODULE', 1350);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_PLUGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_PLUGIN', 1450);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_THEME')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_THEME', 1500);
}
if (defined('MOLAJO_ASSET_TYPE_EXTENSION_END')) {
} else {
    define('MOLAJO_ASSET_TYPE_EXTENSION_END', 1999);
}

/** menu item types */
if (defined('MOLAJO_ASSET_TYPE_MENU_ITEM_BEGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_MENU_ITEM_BEGIN', 2000);
}
if (defined('MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT')) {
} else {
    define('MOLAJO_ASSET_TYPE_MENU_ITEM_COMPONENT', 2000);
}
if (defined('MOLAJO_ASSET_TYPE_MENU_ITEM_LINK')) {
} else {
    define('MOLAJO_ASSET_TYPE_MENU_ITEM_LINK', 2100);
}
if (defined('MOLAJO_ASSET_TYPE_MENU_ITEM_MODULE')) {
} else {
    define('MOLAJO_ASSET_TYPE_MENU_ITEM_MODULE', 2200);
}
if (defined('MOLAJO_ASSET_TYPE_MENU_ITEM_SEPARATOR')) {
} else {
    define('MOLAJO_ASSET_TYPE_MENU_ITEM_SEPARATOR', 2300);
}
if (defined('MOLAJO_ASSET_TYPE_MENU_ITEM_END')) {
} else {
    define('MOLAJO_ASSET_TYPE_MENU_ITEM_END', 2999);
}

/** menu item types */
if (defined('MOLAJO_ASSET_TYPE_CATEGORY_BEGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_CATEGORY_BEGIN', 3000);
}
if (defined('MOLAJO_ASSET_TYPE_CATEGORY_LIST')) {
} else {
    define('MOLAJO_ASSET_TYPE_CATEGORY_LIST', 3000);
}
if (defined('MOLAJO_ASSET_TYPE_CATEGORY_TAG')) {
} else {
    define('MOLAJO_ASSET_TYPE_CATEGORY_TAG', 3500);
}
if (defined('MOLAJO_ASSET_TYPE_CATEGORY_END')) {
} else {
    define('MOLAJO_ASSET_TYPE_CATEGORY_END', 3999);
}

/** content */
if (defined('MOLAJO_ASSET_TYPE_CONTENT_BEGIN')) {
} else {
    define('MOLAJO_ASSET_TYPE_CONTENT_BEGIN', 10000);
}
if (defined('MOLAJO_ASSET_TYPE_CONTENT_ARTICLE')) {
} else {
    define('MOLAJO_ASSET_TYPE_CONTENT_ARTICLE', 10000);
}
if (defined('MOLAJO_ASSET_TYPE_CONTENT_CONTACT')) {
} else {
    define('MOLAJO_ASSET_TYPE_CONTENT_CONTACT', 20000);
}
if (defined('MOLAJO_ASSET_TYPE_CONTENT_COMMENT')) {
} else {
    define('MOLAJO_ASSET_TYPE_CONTENT_COMMENT', 30000);
}
if (defined('MOLAJO_ASSET_TYPE_CONTENT_MEDIA')) {
} else {
    define('MOLAJO_ASSET_TYPE_CONTENT_MEDIA', 40000);
}
if (defined('MOLAJO_ASSET_TYPE_CONTENT_END')) {
} else {
    define('MOLAJO_ASSET_TYPE_CONTENT_END', 999999);
}

/**
 *  AUTHENTICATION
 */
define('MOLAJO_AUTHENTICATE_STATUS_SUCCESS', 1);
define('MOLAJO_AUTHENTICATE_STATUS_CANCEL', 2);
define('MOLAJO_AUTHENTICATE_STATUS_FAILURE', 4);

/**
 *  SYSTEM GROUPS
 */
if (defined('MOLAJO_SYSTEM_GROUP_PUBLIC')) {
} else {
    define('MOLAJO_SYSTEM_GROUP_PUBLIC', 1);
}
if (defined('MOLAJO_SYSTEM_GROUP_GUEST')) {
} else {
    define('MOLAJO_SYSTEM_GROUP_GUEST', 2);
}
if (defined('MOLAJO_SYSTEM_GROUP_REGISTERED')) {
} else {
    define('MOLAJO_SYSTEM_GROUP_REGISTERED', 3);
}
if (defined('MOLAJO_SYSTEM_GROUP_ADMINISTRATOR')) {
} else {
    define('MOLAJO_SYSTEM_GROUP_ADMINISTRATOR', 4);
}

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
