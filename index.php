<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO', 'Long Live Molajo!');

@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', 0);

define('MOLAJO_BASE_FOLDER', strtolower(__DIR__));

/*                                              */
/*  Server Super Globals                        */
/*                                              */
$protocol = 'http';
$siteBase = '';
if (isset($_SERVER['HTTPS'])) {
    $protocol .= 's';
}
$protocol .= '://';
if (isset($_SERVER['SERVER_NAME'])) {
    $siteBase = $_SERVER['SERVER_NAME'];
}
if (isset($_SERVER['SERVER_PORT'])) {
    if ($_SERVER['SERVER_PORT'] == '80') {
    } else {
        $siteBase .= ":" . $_SERVER['SERVER_PORT'];
    }
}
if (strripos($_SERVER['PHP_SELF'], '/index.php')) {
    $folder = substr($_SERVER['PHP_SELF'], 0, strripos($_SERVER['PHP_SELF'], '/index.php')).'/';
} else {
    $folder = '/';
}
/* use $sitebase to identify site */
$siteBase .= $folder;

define('MOLAJO_BASE_URL', strtolower($protocol.$siteBase));

/**
 *  Override folder locations using a new defines.php file
 *  on the base folder that identifies the following:
 *
 *  - applications - define MOLAJO_APPLICATIONS
 *  - extensions - define MOLAJO_EXTENSIONS
 *  - platforms - define PLATFORMS
 *  - sites - define SITES
 *
 * Also,
 *  - update the SITES/sites.xml file folderpath value
 */
if (file_exists(MOLAJO_BASE_FOLDER . '/defines.php')) {
    include_once MOLAJO_BASE_FOLDER . '/defines.php';
}
if (defined('MOLAJO_APPLICATIONS')) {
} else {
    define('MOLAJO_APPLICATIONS', MOLAJO_BASE_FOLDER . '/applications');
}
if (defined('MOLAJO_EXTENSIONS')) {
} else {
    define('MOLAJO_EXTENSIONS', MOLAJO_BASE_FOLDER . '/extensions');
}
if (defined('PLATFORMS')) {
} else {
    define('PLATFORMS', MOLAJO_BASE_FOLDER . '/platforms');
}
if (defined('SITES')) {
} else {
    define('SITES', MOLAJO_BASE_FOLDER . '/sites');
}

/*  Site, Application, and MOLAJO_PAGE_REQUEST  */
include_once MOLAJO_APPLICATIONS . '/includes/idsite.php';
include_once MOLAJO_APPLICATIONS . '/includes/idapp.php';

/*  Lazy Load Classes                           */
require_once MOLAJO_APPLICATIONS . '/includes/loadclasses.php';

/**
 *  Go Molajo.
 */
Molajo::Site();
