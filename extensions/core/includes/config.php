<?php
/**
 * @package     Molajo
 * @subpackage  Configuration
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Molajo
 */
if (class_exists('MolajoVersion')) {
} else {
    require_once MOLAJO_EXTENSIONS_CORE . '/includes/version.php';
}
if (class_exists('MolajoFactory')) {
} else {
    require_once MOLAJO_APPLICATION_CORE . '/factory.php';
}

/**
 *  Configuration File and Error Reporting
 */
if (MOLAJO_APPLICATION == 'installation') {
    define('JDEBUG', false);
} else {

    /** site configuration */
    if (file_exists(MOLAJO_SITE_FOLDER_PATH . '/configuration.php')) {
    } else {
        echo 'Molajo configuration.php File Missing';
        exit;
    }
    require_once MOLAJO_SITE_FOLDER_PATH . '/configuration.php';

    $CONFIG = new MolajoSiteConfiguration();

    if (@$CONFIG->error_reporting === 0) {
        error_reporting(0);

    } else if (@$CONFIG->error_reporting > 0) {
        error_reporting($CONFIG->error_reporting);
        ini_set('display_errors', 1);
    }

    define('JDEBUG', $CONFIG->debug);

    unset($CONFIG);
    if (JDEBUG) {
        $_PROFILER = JProfiler::getInstance('Application');
    }
}
