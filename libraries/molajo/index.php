<?php
/**
 * @version     $id: index.php
 * @package     Molajo Library
 * @subpackage  Index.php
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/** LOAD */

/** phpversion */
require_once MOLAJO_LIBRARY.'/includes/phpversion.php';
/** defines */
require_once MOLAJO_LIBRARY.'/includes/defines.php';
/** joomla platform */
require_once JPATH_LIBRARIES.'/import.php';
/** molajo and joomla platform */
require_once MOLAJO_LIBRARY.'/includes/import.php';
/** other libraries */
require_once MOLAJO_LIBRARY.'/includes/other.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/** INITIALIZE */
$app = JFactory::getApplication(MOLAJO_APPLICATION);
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

    $component = JRequest::getCmd('option', 'com_articles');
    if ($component == 'com_articles') {
        JRequest::setVar('option', 'com_articles');
    }
}

/** DISPATCH */
if (MOLAJO_APPLICATION == 'installation') {
} else {
    $app->dispatch();
    JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
}

/** RENDER */
$app->render();
JDEBUG ? $_PROFILER->mark('afterRender') : null;

/** complete */
echo $app;
