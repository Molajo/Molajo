<?php
/**
 * @version     $id: joomla.php
 * @package     Molajo
 * @subpackage  Load Joomla Framework
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die();

/** php overrides */
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

/** installation check */
define('INSTALL_CHECK', false);
if (MOLAJO_APPLICATION == 'installation'
    || (INSTALL_CHECK === false && file_exists(JPATH_CONFIGURATION.'/configuration.php')) ) {

} else {
    if (!file_exists(JPATH_CONFIGURATION.'/configuration.php')
        || (filesize(JPATH_CONFIGURATION.'/configuration.php') < 10)
        || file_exists(JPATH_INSTALLATION.'/index.php')) {

        if (MOLAJO_APPLICATION == 'site') {
            $redirect = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'index.php')).'installation/index.php';
        } else {
            $redirect = '../installation/index.php';
        }
        header('Location: '.$redirect);
        exit();
    }
}

if (class_exists('JLoader')) {
} else {
	require_once JPATH_PLATFORM.'/loader.php';
}

JLoader::import('joomla.base.object');
JLoader::import('joomla.base.observable');
JLoader::import('joomla.environment.request');
if (!defined('_JREQUEST_NO_CLEAN')) {
	JRequest::clean();
}
JLoader::import('joomla.environment.response');
JLoader::import('joomla.factory');
$os = strtoupper(substr(PHP_OS, 0, 3));
if (!defined('IS_WIN')) {
	define('IS_WIN', ($os === 'WIN') ? true : false);
}
if (!defined('IS_MAC')) {
	define('IS_MAC', ($os === 'MAC') ? true : false);
}
if (!defined('IS_UNIX')) {
	define('IS_UNIX', (($os !== 'MAC') && ($os !== 'WIN')) ? true : false);
}

/** Register the JPlatform version class for lazy loading */
if (!class_exists('JPlatform')) {
	JLoader::register('JPlatform', JPATH_PLATFORM.'/joomla/platform.php');
}

// Define the Joomla Platform version if not already defined.
if (!defined('JPLATFORM')) {
	define('JPLATFORM', JPlatform::getShortVersion());
}
define('MolajoVersion', '1.0');
if (!class_exists('MolajoVersion')) {
    require JPATH_ROOT.'/includes/version.php';
}
JLoader::import('joomla.error.error');
JLoader::import('joomla.error.exception');
JLoader::import('joomla.utilities.arrayhelper');
JLoader::import('joomla.filter.filterinput');
JLoader::import('joomla.filter.filteroutput');
JLoader::register('JText', JPATH_PLATFORM.'/joomla/methods.php');
JLoader::register('JRoute', JPATH_PLATFORM.'/joomla/methods.php');

/** configuration and debugging */
if (MOLAJO_APPLICATION == 'installation') {
    define('JDEBUG', false);
} else {

    if (file_exists(JPATH_CONFIGURATION.'/configuration.php')) {
    } else {
        echo 'Molajo configuration.php File Missing';
        exit;
    }
    require_once JPATH_CONFIGURATION.'/configuration.php';

    $CONFIG = new JConfig();
    if (@$CONFIG->error_reporting === 0) {
        error_reporting(0);
    } else if (@$CONFIG->error_reporting > 0) {
        error_reporting($CONFIG->error_reporting);
        ini_set('display_errors', 1);
    }
    define('JDEBUG', $CONFIG->debug);
    unset($CONFIG);
    if (JDEBUG) {
        jimport('joomla.error.profiler');
        $_PROFILER = JProfiler::getInstance('Application');
    }
}

/** joomla library: core and overrides */
/** access */
jimport('molajo.overrides.user.user');
jimport('joomla.environment.uri');
jimport('joomla.html.html');
jimport('joomla.utilities.utility');
jimport('joomla.event.event');
jimport('joomla.event.dispatcher');
jimport('joomla.language.language');
jimport('joomla.language.helper');
jimport('joomla.utilities.string');
jimport('joomla.utilities.date');

/** cache */
jimport('joomla.cache.cache');
jimport('joomla.cache.controller');
jimport('joomla.cache.storage');
JLoader::register('JCache', OVERRIDES_LIBRARY.'/cache/cache.php');
JLoader::register('JCacheController', JPATH_PLATFORM.'/joomla/cache/controller.php');
JLoader::register('JCacheStorage', JPATH_PLATFORM.'/joomla/cache/storage.php');

/** Files and Folders */
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/** database */
jimport('joomla.database');
jimport('molajo.table.table');
jimport('molajo.overrides.database.table');
jimport('molajo.table.user');
jimport('molajo.overrides.database.table.user');
jimport('molajo.table.usergroup');
jimport('molajo.overrides.database.table.usergroup');
jimport('molajo.table.viewlevel');
jimport('molajo.overrides.database.table.viewlevel');

/** file helper */
if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(MOLAJO_LIBRARY.'/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', MOLAJO_LIBRARY.'/helpers/file.php');
    } else {
        JError::raiseNotice(500, JText::_('MOLAJO_OVERRIDE_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
$filehelper = new MolajoFileHelper();

/** categories */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/categories.php', 'MolajoCategories');
/** Helpers */
$files = JFolder::files(MOLAJO_LIBRARY.'/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))).'Helper');
}

jimport('molajo.overrides.application.component.helper');
jimport('molajo.overrides.application.module.helper');
jimport('molajo.overrides.application.categories');
jimport('molajo.overrides.application.helper');
jimport('molajo.overrides.plugin.helper');
jimport('molajo.overrides.user.helper');

/** HTML */
jimport('molajo.overrides.html.pagination');
jimport('molajo.overrides.html.toolbar');
jimport('molajo.overrides.html.editor');

/** Form */
jimport('joomla.form.form');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
jimport('joomla.form.formrule');

/** Utilities */
jimport('joomla.utilities.arrayhelper');
jimport('joomla.registry.registry');

/** Plugins */
jimport('joomla.plugin.plugin');

/** Molajo */
require_once MOLAJO_LIBRARY.'/includes/molajo.php';

/** ACL */
jimport('molajo.overrides.access.access');

/** menu */
jimport('molajo.menu.menu');
jimport('molajo.overrides.application.menu');

/** toolbar */
if (MOLAJO_APPLICATION == 'administrator') {
    require_once JPATH_BASE.'/includes/helper.php';
}
require_once OVERRIDES_LIBRARY.'/includes/toolbar.php';

/** file helper */
if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(MOLAJO_LIBRARY.'/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', MOLAJO_LIBRARY.'/helpers/file.php');
    } else {
        JError::raiseNotice(500, JText::_('MOLAJO_OVERRIDE_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
$filehelper = new MolajoFileHelper();

/** form fields */
$files = JFolder::files(OVERRIDES_LIBRARY.'/form/fields', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/form/fields/', 'JFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(OVERRIDES_LIBRARY.'/form/rules', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/form/rules/', 'JFormRules'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** html fields */
$files = JFolder::files(OVERRIDES_LIBRARY.'/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/html/html/', 'JHtml'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/html/toolbar/button.php', 'JButton');

$files = JFolder::files(OVERRIDES_LIBRARY.'/html/toolbar/button', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/html/toolbar/button/', 'JButton'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** application */
jimport('joomla.application.component.controller');
jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');
jimport('joomla.application.component.model');
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.modelform');
jimport('joomla.application.component.modelitem');
jimport('joomla.application.component.modellist');
jimport('joomla.application.component.view');
jimport('joomla.application.pathway');
jimport('joomla.application.router');

require_once MOLAJO_LIBRARY.'/includes/other.php';