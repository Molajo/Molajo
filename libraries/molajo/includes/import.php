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

define('MolajoVersion', '1.0');
if (!class_exists('MolajoVersion')) {
    require JPATH_ROOT.'/includes/version.php';
}

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

/** joomla library: core and overrides */
JLoader::import('joomla.base.observable');

/** environment */
jimport('joomla.environment.uri');
jimport('joomla.environment.response');
jimport('joomla.filter.filterinput');
jimport('joomla.filter.filteroutput');

/** Utilities */
jimport('joomla.utilities.arrayhelper');
jimport('joomla.utilities.buffer');
jimport('joomla.utilities.date');
jimport('joomla.utilities.simplecrypt');
jimport('joomla.utilities.simplexml');
jimport('joomla.utilities.string');
jimport('joomla.utilities.utility');
jimport('joomla.utilities.xmlelement');

/** registry */
jimport('joomla.registry.registry');

/** Language */
jimport('joomla.language.language');
jimport('joomla.language.helper');

/** Filesystem */
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

/** Cache */
jimport('joomla.cache.cache');
jimport('joomla.cache.controller');
jimport('joomla.cache.storage');

/** Database */
jimport('joomla.database');



jimport('joomla.database.table');
jimport('joomla.database.tableasset');

$files = JFolder::files(MOLAJO_LIBRARY.'/table', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/table/table.php', 'MolajoTable');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/table/'.$file, 'MolajoTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(OVERRIDES_LIBRARY.'/database', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php') {
        $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/database/table.php', 'JTable');
    } else {
        $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/database/'.$file, 'JTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** User */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/user.php', 'MolajoUserHelper');
jimport('molajo.overrides.user.helper');
jimport('molajo.overrides.user.user');

/** ACL */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/core.php', 'CoreACL');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/molajo.php', 'MolajoACL');
jimport('molajo.overrides.access.access');

/** Application */
if (MOLAJO_APPLICATION == 'administrator') {
    require_once JPATH_BASE.'/includes/helper.php';
}
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/administrator.php', 'MolajoAdministrator');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/site.php', 'MolajoSite');
jimport('joomla.application.application');
jimport('joomla.application.applicationexception');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/application.php', 'MolajoApplicationHelper');
jimport('molajo.overrides.application.helper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/component.php', 'MolajoComponentHelper');
jimport('molajo.overrides.application.component.helper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/module.php', 'MolajoModuleHelper');
jimport('molajo.overrides.application.module.helper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/menu/menu.php', 'MolajoMenu');
jimport('molajo.overrides.application.menu');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/categories.php', 'MolajoCategories');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/categories.php', 'MolajoCategoriesHelper');
jimport('molajo.overrides.application.categories');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/router.php', 'MolajoRouter');
jimport('molajo.overrides.application.router');
jimport('joomla.plugin.plugin');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/plugin.php', 'MolajoPluginHelper');
jimport('molajo.overrides.plugin.helper');

/** events and plugins */
jimport('joomla.event.event');
jimport('joomla.event.dispatcher');

/** Mail */
jimport('joomla.mail.mail');

/** Other Helpers */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/date.php', 'MolajoDateHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/filter.php', 'MolajoFilterHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/image.php', 'MolajoImageHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/oembed.php', 'MolajoOembedHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/query.php', 'MolajoQueryHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/submenu.php', 'MolajoSubmenuHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/text.php', 'MolajoTextHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/url.php', 'MolajoURLHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/validate.php', 'MolajoValidateHelper');

/** Form */
jimport('joomla.form.form');
jimport('joomla.form.formfield');
jimport('joomla.form.formrule');
jimport('joomla.form.helper');
/** Form Fields */
$files = JFolder::files(MOLAJO_LIBRARY.'/form/fields', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/form/fields/', 'JFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(OVERRIDES_LIBRARY.'/form/fields', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/form/fields/', 'JFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
/** Form Rules */
$files = JFolder::files(OVERRIDES_LIBRARY.'/form/rules', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/form/rules/', 'JFormRules'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** HTML */
jimport('joomla.html.html');
jimport('joomla.html.toolbar');
jimport('molajo.overrides.html.pagination');
jimport('molajo.overrides.html.toolbar');
jimport('molajo.overrides.html.editor');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/toolbar.php', 'MolajoToolbarHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/rendertoolbar.php', 'MolajoRendertoolbarHelper');
require_once OVERRIDES_LIBRARY.'/includes/toolbar.php';

/** HTML fields */
$files = JFolder::files(MOLAJO_LIBRARY.'/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/html/html/', 'JHtml'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

$files = JFolder::files(OVERRIDES_LIBRARY.'/html/html', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/html/html/', 'JHtml'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/html/toolbar/button.php', 'JButton');

$files = JFolder::files(OVERRIDES_LIBRARY.'/html/toolbar/button', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/html/toolbar/button/', 'JButton'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Controller */
$files = JFolder::files(MOLAJO_LIBRARY.'/controllers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/controllers/controller.php', 'MolajoController');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/controllers/'.$file, 'MolajoController'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Fields */
$files = JFolder::files(MOLAJO_LIBRARY.'/fields', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'field.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/fields/field.php', 'MolajoField');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/fields/'.$file, 'MolajoField'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
/** Models */
$files = JFolder::files(MOLAJO_LIBRARY.'/models', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/models/'.$file, 'MolajoModel'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
/** Model-Elements */
$files = JFolder::files(MOLAJO_LIBRARY.'/models/elements', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/models/elements/'.$file, 'MolajoElement'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Views */
$files = JFolder::files(MOLAJO_LIBRARY.'/views', '\.php$', false, false);
$format = JRequest::getCmd('format', 'html');
if ($format == 'html' || $format == 'feed' || $format == 'raw') {
} else {
    $format == 'raw';
}
foreach ($files as $file) {

    if ($file == 'view.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/views/view.php', 'MolajoView');
    } else {
        if (strpos($file, $format)) {
            $filehelper->requireClassFile(MOLAJO_LIBRARY.'/views/'.$file, 'MolajoView'.ucfirst(substr($file, 0, strpos($file, '.'))));
        }
    }
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

/** Router */
$files = JFolder::files(MOLAJO_LIBRARY.'/router', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'router.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/router/router.php', 'MolajoRouter');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/router/'.$file, 'MolajoRouter'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
