<?php
/**
 * @package     Molajo
 * @subpackage  Application Flow
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** LOAD */
require_once LIBRARIES.'/includes/phpversion.php';
require_once LIBRARIES.'/includes/defines.php';
require_once LIBRARIES.'/includes/import.php';
require_once LIBRARIES.'/includes/other.php';
require_once LIBRARIES.'/includes/overrides.php';
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/** INITIALIZE */
$app = MolajoFactory::getApplication(MOLAJO_APPLICATION);

if (MOLAJO_APPLICATION == 'administrator') {
    $app->initialise(array(
        'language' => $app->getUserState('application.language', 'language')
    ));
} else {
    $app->initialise();
}
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;

/** ROUTE */
$app->route();
JDEBUG ? $_PROFILER->mark('afterRoute') : null;

/** DISPATCH */
$app->dispatch();
JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
 
/** RENDER */
$app->render();
JDEBUG ? $_PROFILER->mark('afterRender') : null;

/** complete */
echo $app;
