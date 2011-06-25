<?php
/**
 * @version     $id: index.php
 * @package     Molajo Library
 * @subpackage  Index.php
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/** phpversion */
require_once MOLAJO_LIBRARY.'/includes/phpversion.php';
/** defines */
require_once MOLAJO_LIBRARY.'/includes/defines.php';
/** joomla platform */
require_once JPATH_LIBRARIES.'/import.php';
/** load frameworks */
require_once MOLAJO_LIBRARY.'/includes/import.php';
/** other libraries */
require_once MOLAJO_LIBRARY.'/includes/other.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/** initialize */
$app = JFactory::getApplication(MOLAJO_APPLICATION);

if (MOLAJO_APPLICATION == 'administrator') {
    $app->initialise(array(
        'language' => $app->getUserState('application.lang', 'lang')
    ));
} else {
    $app->initialise();
}
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;

/** route application */
if (MOLAJO_APPLICATION == 'installation') {
} else {
    $app->route();
    JDEBUG ? $_PROFILER->mark('afterRoute') : null;
}

// Get the component if not set.
$component = JRequest::getCmd('option', 'com_articles');
if ($component == 'com_articles') {
     JRequest::setVar('option', 'com_articles');
}

/** dispatch application */
if (MOLAJO_APPLICATION == 'installation') {
} else {
    $app->dispatch();
    JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
}

/** render application */
$app->render();
JDEBUG ? $_PROFILER->mark('afterRender') : null;

/** complete */
echo $app;
