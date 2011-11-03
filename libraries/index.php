<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
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
 	    $siteURL .= ":".$_SERVER['SERVER_PORT'];
    }
}

if (defined('MOLAJO_SITE')) {
} else {
    $xml = simplexml_load_file(MOLAJO_BASE_FOLDER.'/sites.xml', 'SimpleXMLElement');
    $count = $xml->count;
    for ($i = 1; $i < $count + 1; $i++) {
        $fieldName = 'site'.$i;
        if ($siteURL == $xml->$fieldName) {
            define('MOLAJO_SITE', $i);
            break;
        }
    }
}

/**
 *  Load Framework Classes
 */
require_once LIBRARIES.'/includes/phpversion.php';
require_once LIBRARIES.'/includes/defines.php';
require_once LIBRARIES.'/includes/installcheck.php';
define('JPATH_PLATFORM', LIBRARIES.'/jplatform');
require_once JPATH_PLATFORM.'/platform.php';
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

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/**
 *  Get Site
 */
$site = MolajoFactory::getSite(MOLAJO_SITE);
JDEBUG ? $_PROFILER->mark('afterGetSite') : null;

/**
 *  Initialize Site
 */
$site->initialise();
JDEBUG ? $_PROFILER->mark('afterSiteInitialise') : null;

/**
 *  Get Application
 */
$app = MolajoFactory::getApplication(MOLAJO_APPLICATION);
JDEBUG ? $_PROFILER->mark('afterGetApplication') : null;

/**
 *  Initialize Application
 */
$app->initialise();
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

