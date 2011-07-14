<?php
/**
 * @package     Molajo
 * @subpackage  index.php
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO_APPLICATION', 'site');

/**
 * BEGIN: Common code for all Applications
 *
 * JPATH_BASE (same as JPATH_ADMINISTRATOR, JPATH_INSTALLATION, JPATH_SITE)
 * JPATH_ROOT is the root path for the Joomla install regardless of BASE
 *
 */
define('MOLAJO', 'molajo');
define('DS', DIRECTORY_SEPARATOR);

/** Override defines.php for Application in current folder */
if (file_exists(dirname(__FILE__).'/defines.php')) {
	include_once dirname(__FILE__).'/defines.php';
}

/**
 * JPATH_BASE - base for the Application, examples:
 * 0 /Users/amystephen/Sites/molajo/
 * 1 /Users/amystephen/Sites/molajo/administrator
 * 2 /Users/amystephen/Sites/molajo/installation
 */
define('JPATH_BASE', dirname(__FILE__));

/**
 * JPATH_ROOT - base for the website, example:
 * ex /Users/amystephen/Sites/molajo
 */
if (MOLAJO_APPLICATION == 'site') {
    define('JPATH_ROOT', JPATH_BASE);
} else {
    $parts = explode(DS, JPATH_BASE);
    array_pop($parts);
    define('JPATH_ROOT', implode(DS, $parts));
}

/** Molajo Library */
define('MOLAJO_LIBRARY', JPATH_ROOT.'/libraries/molajo');

/** index.php - shared between Applications */
include_once MOLAJO_LIBRARY.'/index.php';

/**
 * END: Common code for all Applications
 */