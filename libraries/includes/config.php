<?php
/**
 * @package     Molajo
 * @subpackage  Load Molajo Configuration
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  JRequest Clean
 */
if (isset($_SERVER['HTTP_HOST'])) {
	if (defined('_JREQUEST_NO_CLEAN')) {
    } else {
		JRequest::clean();
	}
}

/**
 *  Configuration File and Error Reporting
 */
if (MOLAJO_APPLICATION == 'installation') {
    define('JDEBUG', false);
} else {

    if (file_exists(MOLAJO_PATH_CONFIGURATION.'/configuration.php')) {
    } else {
        echo 'Molajo configuration.php File Missing';
        exit;
    }
    require_once MOLAJO_PATH_CONFIGURATION.'/configuration.php';

    $CONFIG = new MolajoConfig();
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

/**
 *  Molajo
 */
if (class_exists('MolajoVersion')) {
} else {
    require_once LIBRARIES.'/includes/version.php';
}
if (class_exists('MolajoFactory')) {
} else {
    require_once LIBRARIES.'/molajo/application/factory.php';
}