<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

//echo '<pre>';var_dump();'</pre>';

/**
 *  PHP Overrides
 */
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

/**
 *  Multisite logic: Identify site and locate base folder
 */
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

/**
 *  Application
 */
if (defined('MOLAJO_APPLICATIONS')) {
} else {
    define('MOLAJO_APPLICATIONS', MOLAJO_BASE_FOLDER . '/applications');
}
include_once MOLAJO_APPLICATIONS . '/index.php';
