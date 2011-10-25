<?php
/**
 * @package     Molajo
 * @subpackage  Application Flow
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

//echo '<pre>';var_dump();'</pre>';

/** php overrides */
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

require_once LIBRARIES.'/includes/phpversion.php';
require_once LIBRARIES.'/includes/defines.php';
require_once LIBRARIES.'/includes/installcheck.php';
require_once JPATH_PLATFORM.'/platform.php';

/**
 *  Load Classes
 */
require_once LIBRARIES.'/jplatform/loader.php';
if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(MOLAJO_LIBRARY.'/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', MOLAJO_LIBRARY.'/helpers/file.php');
    } else {
        JError::raiseNotice(500, MolajoText::_('MOLAJO_OVERRIDE_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
require_once LIBRARIES.'/includes/joomla.php';
require_once LIBRARIES.'/includes/config.php';
require_once LIBRARIES.'/includes/molajo.php';
require_once LIBRARIES.'/includes/other.php';
require_once LIBRARIES.'/includes/overrides.php';

//require LIBRARIES.'/Doctrine/Common/ClassLoader.php';
//$classLoader = new \Doctrine\Common\ClassLoader('Doctrine');
//var_dump($classLoader);
//$classLoader->register();

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/**
 *  Application
 */
$app = MolajoFactory::getApplication(MOLAJO_APPLICATION);
JDEBUG ? $_PROFILER->mark('afterGetApplication') : null;

/**
 *  Initialize
 */
if (MOLAJO_APPLICATION == 'administrator') {
    $app->initialise(array(
        'language' => $app->getUserState('application.language', 'language')
    ));
} else {
    $app->initialise();
}
JDEBUG ? $_PROFILER->mark('afterInitialise') : null;

/**
 *  Route
 */
$app->route();
JDEBUG ? $_PROFILER->mark('afterRoute') : null;

/**
 *  Dispatch
 */
$app->dispatch();
JDEBUG ? $_PROFILER->mark('afterDispatch') : null;
 
/**
 *  Render
 */
$app->render();
JDEBUG ? $_PROFILER->mark('afterRender') : null;

/**
 *  Display
 */
echo $app;
