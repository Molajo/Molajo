<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  Load Classes
 */
if (defined('MOLAJO_APPLICATIONS_CORE')) {
} else {
    define('MOLAJO_APPLICATIONS_CORE', MOLAJO_APPLICATIONS . '/molajo');
}
require_once MOLAJO_APPLICATIONS_CORE . '/includes/phpversion.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/defines.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/installcheck.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/joomla.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/config.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/application.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/site.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/platform.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/other.php';
require_once MOLAJO_APPLICATIONS_CORE . '/includes/overrides.php';

JDEBUG ? $_PROFILER->mark('afterLoad') : null;

/**
 *  Get Site
 */
$site = MolajoFactory::getSite();
JDEBUG ? $_PROFILER->mark('afterGetSite') : null;

/**
 *  Initialize Site
 */
$site->initialise();
JDEBUG ? $_PROFILER->mark('afterSiteInitialise') : null;

/**
 *  Get Application
 */
$app = MolajoFactory::getApplication();
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
print_r(get_defined_constants(true));
 */
/**
 *  Display
 */
echo $app;

