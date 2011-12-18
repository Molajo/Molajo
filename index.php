<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
if (defined('MOLAJO_APPLICATION')) {
} else {
    define('MOLAJO_APPLICATION', 'site');
    define('DS', DIRECTORY_SEPARATOR);
}
define('MOLAJO', 'Long Live Molajo!');

/** Base Folder and URL */
define('MOLAJO_BASE_FOLDER', strtolower(dirname(__FILE__)));

$baseURL = 'http';
if (isset($_SERVER['HTTPS'])) {
    $baseURL .= 's';
}
$baseURL .= '://';
if (isset($_SERVER['SERVER_NAME'])) {
    $baseURL .= $_SERVER['SERVER_NAME'];
}
if (isset($_SERVER['SERVER_PORT'])) {
    if ($_SERVER['SERVER_PORT'] == '80') {
    } else {
        $baseURL .= ":" . $_SERVER['SERVER_PORT'];
    }
}
if (isset($_SERVER["REQUEST_URI"])) {
    $baseURL .= $_SERVER['REQUEST_URI'];
}
define('MOLAJO_BASE_URL', strtolower($baseURL));

if (strrpos(MOLAJO_BASE_URL, MOLAJO_APPLICATION)) {
    define('MOLAJO_BASE_URL_NOAPP', substr(MOLAJO_BASE_URL, 0, strrpos(MOLAJO_BASE_URL, MOLAJO_APPLICATION)));
} else {
    define('MOLAJO_BASE_URL_NOAPP', MOLAJO_BASE_URL);
}

/**
 *  OVERRIDE USING THIS FILE
 */
if (file_exists(MOLAJO_BASE_FOLDER . '/defines.php')) {
    include_once MOLAJO_BASE_FOLDER . '/defines.php';
}

/*                                              */
/*  SITES LAYER                                 */
/*                                              */
if (defined('MOLAJO_SITES')) {
} else {
    define('MOLAJO_SITES', MOLAJO_BASE_FOLDER . '/sites/');
}

/** php directives */
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

/** site determination */
$siteURL = $_SERVER['SERVER_NAME'];
if (isset($_SERVER['SERVER_PORT'])) {
    if ($_SERVER['SERVER_PORT'] == '80') {
    } else {
        $siteURL .= ":" . $_SERVER['SERVER_PORT'];
    }
}

if (defined('MOLAJO_SITE')) {
} else {
    $xml = simplexml_load_file(MOLAJO_BASE_FOLDER . '/sites/sites.xml', 'SimpleXMLElement');
    $count = $xml->count;
    for ($i = 1; $i < $count + 1; $i++) {
        $name = 'site' . $i;
        if ($siteURL == $xml->$name) {
            define('MOLAJO_SITE', $i);
            break;
        }
    }
    define('MOLAJO_SITE', 1);
}

if (defined('MOLAJO_SITE_ID')) {
} else {
    define('MOLAJO_SITE_ID', MOLAJO_SITE);
}
if (defined('MOLAJO_SITE_PATH')) {
} else {
    define('MOLAJO_SITE_PATH', MOLAJO_BASE_FOLDER . '/sites/' . MOLAJO_SITE);
}
if (defined('MOLAJO_SITE_CORE')) {
} else {
    define('MOLAJO_SITE_CORE', MOLAJO_BASE_FOLDER . '/sites/core');
}

/*                                              */
/*  APPLICATIONS LAYER                          */
/*                                              */
if (defined('MOLAJO_APPLICATION_CORE')) {
} else {
    define('MOLAJO_APPLICATION_CORE', MOLAJO_BASE_FOLDER . '/applications');
}

/*                                              */
/*  EXTENSIONS LAYER                            */
/*                                              */
if (defined('MOLAJO_EXTENSIONS_CORE')) {
} else {
    define('MOLAJO_EXTENSIONS_CORE', MOLAJO_BASE_FOLDER . '/extensions/core');
}

require_once MOLAJO_EXTENSIONS_CORE.'/includes/phpversion.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/defines.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/installcheck.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/platforms-joomla.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/config.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/applications.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/extensions.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/sites.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/platforms-molajo.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/platforms-twig.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/platforms-mustache.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/platforms-doctrine.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/platforms-simple-pie.php';
require_once MOLAJO_EXTENSIONS_CORE.'/includes/overrides.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/**
 *  Get the Site
 */
$site = MolajoFactory::getSite(MOLAJO_SITE_ID);
JDEBUG ? $_PROFILER->mark('afterGetSite') : null;

/**
 *  Initialise the Site
 */
$site->initialise();
JDEBUG ? $_PROFILER->mark('afterSiteInitialise') : null;

/**
 *  Get the Application
 */
$app = MolajoFactory::getApplication(MOLAJO_APPLICATION);
JDEBUG ? $_PROFILER->mark('afterGetApplication') : null;

/**
 *  Initialize Application
 */
$app->initialise();
JDEBUG ? $_PROFILER->mark('afterInitialiseApplication') : null;

/**
 *  Execute Extension Layer
 */
$extension = new MolajoExtension();
JDEBUG ? $_PROFILER->mark('afterExtensionInitialise') : null;

/**
 *  Application Response
 */
$app->respond();
JDEBUG ? $_PROFILER->mark('afterExecute') : null;