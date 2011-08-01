<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO_APPLICATION', 'installation');
define('MOLAJO_APPLICATION_ID', 2);

/**
 * BEGIN: Common code for all Applications
 *
 * MOLAJO_PATH_BASE 
 *  Same as JPATH_ADMINISTRATOR, JPATH_INSTALLATION, JPATH_SITE
 * MOLAJO_PATH_ROOT root path for the Molajo website regardless of BASE
 */
define('MOLAJO', 'molajo');
define('DS', DIRECTORY_SEPARATOR);

/** Override defines.php for Application in current folder */
if (file_exists(dirname(__FILE__).'/defines.php')) {
	include_once dirname(__FILE__).'/defines.php';
}

/**
 * MOLAJO_PATH_BASE - base for the Application, examples:
 * 0 /Users/amystephen/Sites/molajo/
 * 1 /Users/amystephen/Sites/molajo/administrator
 * 2 /Users/amystephen/Sites/molajo/installation
 */
$temp = strtolower(dirname(__FILE__));
define('MOLAJO_PATH_BASE', $temp);

/**
 * MOLAJO_PATH_ROOT - base for the website, example:
 * ex /Users/amystephen/Sites/molajo
 */
if (MOLAJO_APPLICATION == 'site') {
    define('MOLAJO_PATH_ROOT', MOLAJO_PATH_BASE);
} else {
    $parts = explode(DS, MOLAJO_PATH_BASE);
    array_pop($parts);
    define('MOLAJO_PATH_ROOT', implode(DS, $parts));
}

/** Libraries */
define('LIBRARIES', MOLAJO_PATH_ROOT.'/libraries/');

/** index.php - shared between Applications */
include_once LIBRARIES.'/index.php';

/**
 * END: Common code for all Applications
 */