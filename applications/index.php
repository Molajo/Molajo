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
require_once MOLAJO_APPLICATIONS . '/molajo/includes/phpversion.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/defines.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/installcheck.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/joomla.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/config.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/application.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/site.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/platform.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/other.php';
require_once MOLAJO_APPLICATIONS . '/molajo/includes/overrides.php';
require_once MOLAJO_CMS_TEMPLATES . '/maji/index.php';

return;
die;

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
print_r(get_defined_constants(true));
 */
/**
 *  Display
 */
echo $app;

