<?php
/**
 * @package     Molajo
 * @subpackage  Defines
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** Define PHP constants for application variables */
$defines = simplexml_load_file(strtolower(__DIR__) . '/defines.xml', 'SimpleXMLElement');
foreach ($defines->define as $item) {
    if (defined((string)$item['name'])) {
    } else {
        $value = (string)$item['value'];
        define((string)$item['name'], $value);
    }
}

/**
 *  Platform
 */
if (defined('PLATFORM_MOLAJO')) {
} else {
    define('PLATFORM_MOLAJO', PLATFORMS . '/molajo');
}

if (defined('PLATFORM_MUSTACHE')) {
} else {
    define('PLATFORM_MUSTACHE', PLATFORMS . '/Mustache');
}
if (defined('PLATFORM_SYMFONY_EVENT')) {
} else {
    define('PLATFORM_SYMFONY_EVENT', PLATFORMS . '/sfEvent');
}

/**
 *  Applications
 */
if (defined('MOLAJO_APPLICATIONS_MVC')) {
} else {
    define('MOLAJO_APPLICATIONS_MVC', MOLAJO_APPLICATIONS . '/base/mvc');
}
if (defined('MOLAJO_APPLICATIONS_MVC_URL')) {
} else {
    define('MOLAJO_APPLICATIONS_MVC_URL', MOLAJO_BASE_URL . 'applications/base/mvc');
}

/**
 *  Extensions
 */
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
 *  Allows for quoting in language .ini files.
 */
if (defined('_QQ_')) {
} else {
    define('_QQ_', '"');
}

/**
 *  EXTENSION OPTIONS
 *
 *  SOON TO BE REMOVED
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
