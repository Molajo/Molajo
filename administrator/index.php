<?php
/**
 * @package     Molajo
 * @subpackage  index.php
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO_APPLICATION', 'administrator');

/**
 * BEGIN: Common code for all clients
 *
 * JPATH_BASE (same as JPATH_ADMINISTRATOR, JPATH_INSTALLATION, JPATH_SITE)
 * JPATH_ROOT is the root path for the Joomla install regardless of BASE
 *
 */
define('MOLAJO', 'molajo');
define('DS', DIRECTORY_SEPARATOR);

/** Override defines.php for client in current folder */
if (file_exists(dirname(__FILE__).'/defines.php')) {
	include_once dirname(__FILE__).'/defines.php';
}

/** JPATH_BASE - base for the client base ex /Users/amystephen/Sites/molajo/administrator */
define('JPATH_BASE', dirname(__FILE__));

/** JPATH_ROOT - base for the website ex /Users/amystephen/Sites/molajo */
if (MOLAJO_APPLICATION == 'site') {
    define('JPATH_ROOT', JPATH_BASE);
} else {
    $parts = explode(DS, JPATH_BASE);
    array_pop($parts);
    define('JPATH_ROOT', implode(DS, $parts));
}

/** Library */
define('MOLAJO_LIBRARY', JPATH_ROOT.'/libraries/molajo');

/** Index.php - shared between clients */
include_once MOLAJO_LIBRARY.'/index.php';

/**
 * END: Common code for all clients
 */