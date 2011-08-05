<?php
/**
 * @package     Molajo
 * @subpackage  Application Flow
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** LOAD */

/** phpversion */
require_once LIBRARIES.'/includes/phpversion.php';
/** defines */
require_once LIBRARIES.'/includes/defines.php';
/** factory */
require_once LIBRARIES.'/molajo/factory.php';
require_once LIBRARIES.'/overrides/factory.php';
/** joomla platform */
require_once JPATH_PLATFORM.'/import.php';
/** molajo and joomla platform */
require_once LIBRARIES.'/includes/import.php';
/** other libraries */
require_once LIBRARIES.'/includes/other.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/** INITIALIZE */
$app = MolajoFactory::getApplication(MOLAJO_APPLICATION);

if (MOLAJO_APPLICATION == 'administrator') {
    $app->initialise(array(
        'language' => $app->getUserState('application.lang', 'lang')
    ));
} else {
    $app->initialise();
}
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;

/** ROUTE */
if (MOLAJO_APPLICATION == 'installation') {
} else {
    $app->route();
    JDEBUG ? $_PROFILER->mark('afterRoute') : null;
}

/** DISPATCH */
if (MOLAJO_APPLICATION == 'installation') {
    if (defined('JPATH_COMPONENT')) {
    } else {
        define('JPATH_COMPONENT', MOLAJO_PATH_ROOT.'/'.MOLAJO_APPLICATION_PATH);
    }
} else {
    $app->dispatch();
    JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
}

/** RENDER */
$app->render();
JDEBUG ? $_PROFILER->mark('afterRender') : null;
 
/** complete */
echo $app;
