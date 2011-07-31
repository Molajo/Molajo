<?php
/**
 * @package     Molajo
 * @subpackage  Load Framework
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/** php overrides */
@ini_set('magic_quotes_runtime', 0);
@ini_set('zend.ze1_compatibility_mode', '0');

if (!class_exists('MolajoVersion')) {
    require LIBRARIES.'/includes/version.php';
}

/**
 *  Installation Check
 */

define('INSTALL_CHECK', false);
if (MOLAJO_APPLICATION == 'installation'
    || (INSTALL_CHECK === false
            && file_exists(MOLAJO_PATH_CONFIGURATION.'/configuration.php')) ) {

} else {
    if (!file_exists(MOLAJO_PATH_CONFIGURATION.'/configuration.php')
        || filesize(MOLAJO_PATH_CONFIGURATION.'/configuration.php' < 10)
        || file_exists(MOLAJO_PATH_INSTALLATION.'/index.php')) {

        if (MOLAJO_APPLICATION == 'site') {
            $redirect = substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],'index.php')).'installation/index.php';
        } else {
            $redirect = '../installation/index.php';
        }
        header('Location: '.$redirect);
        exit();
    }
}

/**
 *  Configuration and Debugging
 */
if (MOLAJO_APPLICATION == 'installation') {
    define('JDEBUG', false);
} else {

    if (file_exists(MOLAJO_PATH_CONFIGURATION.'/configuration.php')) {
    } else {
        echo 'Molajo configuration.php File Missing';
        exit;
    }
    require_once MOLAJO_PATH_CONFIGURATION.'/configuration.php';

    $CONFIG = new MolajoConfig();
    if (@$CONFIG->error_reporting === 0) {
        error_reporting(0);
    } else if (@$CONFIG->error_reporting > 0) {
        error_reporting($CONFIG->error_reporting);
        ini_set('display_errors', 1);
    }
    define('JDEBUG', $CONFIG->debug);
    jimport('joomla.error.profiler');
    unset($CONFIG);
    if (JDEBUG) {
        $_PROFILER = JProfiler::getInstance('Application');
    }
}

/**
 *  File Helper
 */
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

/** language */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/language/language.php', 'JLanguage');

/** Application */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/error/profiler.php', 'JProfiler');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/categories.php', 'MolajoCategories');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/categories.php', 'JCategories');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/categories.php', 'MolajoCategoriesHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/application.php', 'MolajoApplication');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/application.php', 'JApplication');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/exception.php', 'MolajoException');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/applicationexception.php', 'ApplicationException');

if (file_exists(MOLAJO_PATH_BASE.'/includes/helper.php')) {
    require_once MOLAJO_PATH_BASE.'/includes/helper.php';
}
if (MOLAJO_APPLICATION == 'installation') {
    require_once MOLAJO_PATH_BASE.'/helpers/database.php';
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/installer/installer.php', 'JInstaller');
}

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/application.php', 'MolajoApplicationHelper');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/helper.php', 'JApplicationHelper');

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/cli.php', 'JCli');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/cli/daemon.php', 'JDaemon');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/component.php', 'MolajoComponentHelper');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/component/helper.php', 'JComponentHelper');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/module.php', 'MolajoModuleHelper');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/module/helper.php', 'JModuleHelper');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/language.php', 'MolajoLanguageHelper');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/language/helper.php', 'JLanguageHelper');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/menu/menu.php', 'MolajoMenu');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/menu.php', 'JMenu');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/includes/router.php', 'MolajoRouter');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/application/router.php', 'JRouter');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/toolbar.php', 'MolajoToolbarHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/rendertoolbar.php', 'MolajoRendertoolbarHelper');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/user.php', 'MolajoUserHelper');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/user/helper.php', 'JUserHelper');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/user/user.php', 'MolajoUser');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/user/user.php', 'JUser');

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/user/authentication.php', 'JAuthentication');

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/environment/uri.php', 'JURI');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/event/event.php', 'JEvent');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/event/dispatcher.php', 'JDispatcher');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filter/filterinput.php', 'JFilterInput');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filter/filteroutput.php', 'JFilterOutput');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/utility.php', 'JUtility');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/string.php', 'JString');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/arrayhelper.php', 'JArrayHelper');

/** Filesystem */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/file.php', 'JFile');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/folder.php', 'JFolder');

$files = JFolder::files(JOOMLA_LIBRARY.'/filesystem', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/'.$file, 'JFilesystemHelper');
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Base */
$files = JFolder::files(JOOMLA_LIBRARY.'/base', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/base/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
/** Database and Table */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database.php', 'JDatabase');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/databaseexception.php', 'DatabaseException');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/databasequery.php', 'JDatabaseQueryElement');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/table/table.php', 'MolajoTable');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/database/table.php', 'JTable');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/table/tablenested.php', 'MolajoTableNested');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/database/tablenested.php', 'JTableNested');

$files = JFolder::files(MOLAJO_LIBRARY.'/table', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php' || $file == 'tablenested.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/table/'.$file, 'MolajoTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(OVERRIDES_LIBRARY.'/database', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php' || $file == 'tablenested.php') {
    } else {
        $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/database/'.$file, 'JTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** mysql */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysqlquery.php', 'JDatabaseQueryMySQL');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysqlexporter.php', 'JDatabaseExporterMySQL');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysqlimporter.php', 'JDatabaseImporterMySQL');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysql.php', 'JDatabaseMySQL');
/** mysqli */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysqliquery.php', 'JDatabaseQueryMySQLi');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysqliexporter.php', 'JDatabaseExporterMySQLi');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysqliimporter.php', 'JDatabaseImporterMySQLi');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/mysqli.php', 'JDatabaseMySQLi');
/** sqlazure */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/sqlazurequery.php', 'JDatabaseQuerySQLAzure');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/sqlazure.php', 'JDatabaseSQLAzure');
/** sqlsrv */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/sqlsrvquery.php', 'JDatabaseQuerySQLSrv');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database/sqlsrv.php', 'JDatabaseSQLSrv');

/** environment */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/environment/response.php', 'JResponse');

/** mail */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/mail/helper.php', 'JMailHelper');
$filehelper->requireClassFile(JPATH_PLATFORM.'/phpmailer/phpmailer.php', 'PHPMailer');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/mail/mail.php', 'MolajoMail');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/mail/mail.php', 'JMail');

/** Utilities */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/utilities/date.php', 'MolajoDate');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/utilities/date.php', 'JDate');
$files = JFolder::files(JOOMLA_LIBRARY.'/utilities', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'date') {
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** registry */
JLoader::register('JRegistryFormat', JOOMLA_LIBRARY.'/registry/format.php');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/registry/registry.php', 'MolajoRegistry');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/registry/registry.php', 'JRegistry');

/** cache */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/cache/controller.php', 'JCacheController');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/cache/storage.php', 'JCacheStorage');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/cache/cache.php', 'JCache');

$files = JFolder::files(JOOMLA_LIBRARY.'/cache/controller', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/cache/controller/'.$file, 'JCacheController'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(JOOMLA_LIBRARY.'/cache/storage', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/cache/storage/'.$file, 'JCacheStorage'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** ACL */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/molajo.php', 'MolajoACL');
$files = JFolder::files(MOLAJO_LIBRARY.'/acl', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'molajo.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/acl/'.$file, 'MolajoACL'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/access/access.php', 'JAccess');

/**
 *  Installer still using JFramework for MVC/JForm/JHTML
 */

if (MOLAJO_APPLICATION == 'installation') {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/html/html.php', 'JHtml');
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/form/formfield.php', 'JFormField');
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/form/formrule.php', 'JFormRule');
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/form/helper.php', 'JFormHelper');
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/form/form.php', 'JForm');
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/form/fields/list.php', 'JFormFieldList');
    $files = JFolder::files(MOLAJO_PATH_BASE.'/models/fields/', '\.php$', false, false);
    foreach ($files as $file) {
        $filehelper->requireClassFile(MOLAJO_PATH_BASE.'/models/fields/'.$file, 'JFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
    $files = JFolder::files(JOOMLA_LIBRARY.'/form/fields/', '\.php$', false, false);
    foreach ($files as $file) {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/form/fields/'.$file, 'JFormField'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
} else {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/formfield.php', 'MolajoFormField');
    $filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/formrule.php', 'MolajoFormRule');
    $filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/helper.php', 'MolajoFormHelper');
    $filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/form.php', 'MolajoForm');

    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/formfield.php', 'MolajoFormField');
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/formrule.php', 'MolajoFormRule');
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/helper.php', 'MolajoFormHelper');
    $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/form.php', 'MolajoForm');
}
 
/** Plugins */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/plugin/plugin.php', 'JPlugin');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/plugin.php', 'MolajoPluginHelper');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/plugin/helper.php', 'JPluginHelper');

/** Mail */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/mail/mail.php', 'JMail');
 
/** Document */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/document.php', 'MolajoDocument');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/document/document.php', 'JDocument');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/renderer.php', 'MolajoDocumentRenderer');
$filehelper->requireClassFile(OVERRIDES_LIBRARY.'/document/renderer.php', 'JDocumentRenderer');

$format = JRequest::getCmd('format', 'html');
$formatClass = 'MolajoDocument'.ucfirst($format);
if (class_exists($formatClass)) {
} else {
    $path = MOLAJO_LIBRARY.'/document/'.$format.'/'.$format.'.php';
    if (file_exists($path)) {
        $formatClass = 'MolajoDocument'.ucfirst($format);
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/'.$format.'/'.$format.'.php', $formatClass);
        $formatClass = 'JDocument'.ucfirst($format);
        $filehelper->requireClassFile(OVERRIDES_LIBRARY.'/document/'.$format.'/'.$format.'.php', $formatClass);
    } else {
        $path = JOOMLA_LIBRARY.'/document/'.$format.'/'.$format.'.php';
        if (file_exists($path)) {
            $filehelper->requireClassFile(JOOMLA_LIBRARY.'/document/'.$format.'/'.$format.'.php', $formatClass);
        } else {
            JError::raiseError(500,JText::_('JLIB_DOCUMENT_ERROR_UNABLE_LOAD_DOC_CLASS'));
        }
    }
}
JRequest::setVar('format', $format);
 
/** Controller */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/controller.php', 'JController');
$files = JFolder::files(MOLAJO_LIBRARY.'/controllers', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/controllers/controller.php', 'MolajoController');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY.'/controllers/'.$file, 'MolajoController'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
/** Fields do not load so that component can override fields, attributes and fieldtypes */

/** Models */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/model.php', 'JModel');
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
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/view.php', 'JView');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/views/view.php', 'MolajoView');
$files = JFolder::files(MOLAJO_LIBRARY.'/views', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'view.php') {
    } else {
        if (strpos($file, $format)) {
            $filehelper->requireClassFile(MOLAJO_LIBRARY.'/views/'.$file, 'MolajoView'.ucfirst(substr($file, 0, strpos($file, '.'))));
        }
    }
}

/** Other Helpers */
$files = JFolder::files(MOLAJO_LIBRARY.'/helpers', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/'.$file, 'Molajo'.ucfirst(substr($file, 0, strpos($file, '.'))).'Helper');
}

/** legacy support */
jimport('joomla.application.component.controlleradmin');
jimport('joomla.application.component.controllerform');
jimport('joomla.application.component.modeladmin');
jimport('joomla.application.component.modelform');
jimport('joomla.application.component.modelitem');
jimport('joomla.application.component.modellist');
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