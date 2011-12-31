<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO', 'Long Live Molajo!');

/** php directives */
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

/** See if this is still needed before release */
define('DS', DIRECTORY_SEPARATOR);

/** Base Folder and URL */
define('MOLAJO_BASE_FOLDER', strtolower(dirname(__FILE__)));

/**
 *  OVERRIDE PATHS TO PRIMARY FOLDERS USING THE FOLLOWING DEFINES.PHP FILES
 *  - applications - define MOLAJO_APPLICATIONS_CORE
 *  - extensions - define MOLAJO_EXTENSIONS_CORE
 *  - platforms - define PLATFORMS
 *  - sites - define MOLAJO_SITES _and_ update the sites.xml file folderpath value
 */
if (file_exists(MOLAJO_BASE_FOLDER . '/defines.php')) {
    include_once MOLAJO_BASE_FOLDER . '/defines.php';
}

/*                                              */
/*  Server Super Globals                        */
/*                                              */
$protocol = 'http';
$siteName = '';
if (isset($_SERVER['HTTPS'])) {
    $protocol .= 's';
}
$protocol .= '://';
if (isset($_SERVER['SERVER_NAME'])) {
    $siteName = $_SERVER['SERVER_NAME'];
}
if (isset($_SERVER['SERVER_PORT'])) {
    if ($_SERVER['SERVER_PORT'] == '80') {
    } else {
        $siteName .= ":" . $_SERVER['SERVER_PORT'];
    }
}
if ($_SERVER['PHP_SELF'] == '/index.php') {
    $folder = '/';
} else {
    $folder = substr($_SERVER['PHP_SELF'], 1, strlen($_SERVER['PHP_SELF']));
    $folder = '/'.substr($folder, 0, strpos($folder, '/')).'/';
}
$siteName .= $folder;

/** base url for this site ex. http://localhost/molajo/ */
define('MOLAJO_BASE_URL', strtolower($protocol.$siteName));

/*                                              */
/*  SITES LAYER                                 */
/*                                              */
if (defined('MOLAJO_SITES')) {
} else {
    define('MOLAJO_SITES', MOLAJO_BASE_FOLDER . '/sites');
}
if (defined('MOLAJO_SHARED_MEDIA')) {
} else {
    define('MOLAJO_SHARED_MEDIA', MOLAJO_SITES . '/media');
}

if (defined('MOLAJO_SITE')) {
} else {
    $sites = simplexml_load_file(MOLAJO_BASE_FOLDER . '/sites.xml', 'SimpleXMLElement');
    foreach ($sites->site as $single) {
        if ($single->name == $siteName) {
            define('MOLAJO_SITE', $single->name);
            define('MOLAJO_SITE_FOLDER_PATH', $single->folderpath);
            define('MOLAJO_SITE_APPEND_TO_BASE_URL', $single->appendtobaseurl);
            define('MOLAJO_SITE_ID', $single->id);
            break;
        }
    }
    if (defined('MOLAJO_SITE')) {
    } else {
        echo 'Fatal Error: Cannot identify site for: '.$siteName;
        die;
    }
}

/*                                              */
/*  APPLICATIONS LAYER                          */
/*                                              */

/**
 * $_SERVER["REQUEST_URI"] everything following host
 *  ex. /molajo/administrator/index.php?option=login
 */
$requestURI = strtolower($_SERVER["REQUEST_URI"]);
/** remove path ex. /molajo/ */
$requestURI = substr($requestURI, strlen($folder), strlen($requestURI) - strlen($folder));
/** extract first node for testing as application name */
if (strpos($requestURI, '/')) {
    $applicationTest = substr($requestURI, 0, strpos($requestURI, '/'));
} else {
    $applicationTest = $requestURI;
}

if (defined('MOLAJO_APPLICATION')) {
} else {
    $apps = simplexml_load_file(MOLAJO_BASE_FOLDER . '/applications.xml', 'SimpleXMLElement');
    foreach ($apps->application as $app) {
        if ($app->name == $applicationTest) {
            define('MOLAJO_APPLICATION', $app->name);
            define('MOLAJO_APPLICATION_URL_PATH', MOLAJO_APPLICATION.'/');
            $pageRequest = substr($requestURI, strlen(MOLAJO_APPLICATION) + 1, strlen($requestURI) - strlen(MOLAJO_APPLICATION) + 1);
            break;
        }
    }
    if (defined('MOLAJO_APPLICATION')) {
    } else {
        define('MOLAJO_APPLICATION', $apps->default->name);
        define('MOLAJO_APPLICATION_URL_PATH', '');
        $pageRequest = $requestURI;
    }
}
define('MOLAJO_PAGE_REQUEST', $pageRequest);

if (defined('MOLAJO_APPLICATIONS_CORE')) {
} else {
    define('MOLAJO_APPLICATIONS_CORE', MOLAJO_BASE_FOLDER . '/applications');
}

/*                                              */
/*  EXTENSIONS LAYER                            */
/*                                              */
if (defined('MOLAJO_EXTENSIONS_CORE')) {
} else {
    define('MOLAJO_EXTENSIONS_CORE', MOLAJO_BASE_FOLDER . '/extensions');
}

require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/phpversion.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/defines.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/installcheck.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/platforms-joomla.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/config.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/applications.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/extensions.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/platforms-molajo.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/platforms-twig.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/platforms-mustache.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/platforms-doctrine.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/platforms-simple-pie.php';
require_once MOLAJO_EXTENSIONS_CORE . '/core/includes/overrides.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

new MolajoController();



