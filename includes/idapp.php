<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/*                                                      */
/*  Identify current application and page request       */
/*                                                      */

/** ex. /molajo/administrator/index.php?option=login    */
$requestURI = strtolower($_SERVER["REQUEST_URI"]);

/** remove folder ex. /molajo/                          */
$requestURI = substr(
    $requestURI,
    strlen(MOLAJO_FOLDER),
    strlen($requestURI) - strlen(MOLAJO_FOLDER)
);

/** extract first node for testing as application name  */
if (strpos($requestURI, '/')) {
    $applicationTest = substr($requestURI, 0, strpos($requestURI, '/'));
} else {
    $applicationTest = $requestURI;
}

$pageRequest = '';
if (defined('MOLAJO_APPLICATION')) {
    /* must also define MOLAJO_PAGE_REQUEST */
} else {
    $apps = simplexml_load_file(MOLAJO_APPLICATIONS . '/Configuration/applications.xml', 'SimpleXMLElement');

    foreach ($apps->application as $app) {

        if ($app->name == $applicationTest) {

            define('MOLAJO_APPLICATION', $app->name);
            define('MOLAJO_APPLICATION_URL_PATH', MOLAJO_APPLICATION . '/');

            $pageRequest = substr(
                $requestURI,
                strlen(MOLAJO_APPLICATION) + 1,
                strlen($requestURI) - strlen(MOLAJO_APPLICATION) + 1
            );
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

/*  Page Request used in Molajo::Request                */
if (defined('MOLAJO_PAGE_REQUEST')) {
} else {
    if (strripos($pageRequest, '/') == (strlen($pageRequest) - 1)) {
        $pageRequest = substr($pageRequest, 0, strripos($pageRequest, '/'));
    }
    define('MOLAJO_PAGE_REQUEST', $pageRequest);
}
