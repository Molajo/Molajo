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

// Detect the native operating system type.
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

if (class_exists('MolajoVersion')) { } else { require LIBRARIES.'/includes/version.php'; }
if (defined('JPATH_PLATFORM')) { } else { define('JPATH_PLATFORM', LIBRARIES.'/jplatform'); }
if (class_exists('JPlatform')) { } else { require_once JPATH_PLATFORM.'/platform.php'; }
if (class_exists('JLoader')) { } else { require_once JPATH_PLATFORM.'/loader.php'; }

require_once LIBRARIES.'/molajo/application/factory.php';
abstract class JFactory extends MolajoFactory {}
/**
 *  Joomla has deprecated JError - good candidate for contributor to replace within Molajo
 */
JLoader::import('joomla.error.error');
JLoader::import('joomla.error.exception');
JLoader::import('joomla.error.profiler');

jimport('joomla.base.observable');
jimport('joomla.event.dispatcher');

if (isset($_SERVER['HTTP_HOST'])) {
	JLoader::import('joomla.environment.request');
	if (defined('_JREQUEST_NO_CLEAN')) {
    } else {
		JRequest::clean();
	}
}

JLoader::import('joomla.base.object');

/** MolajoText */
if (class_exists('MolajoText')) { } else { require_once MOLAJO_LIBRARY.'/application/text.php'; }

/**
 *  File Helper
 */
if (class_exists('MolajoFileHelper')) {
} else {
    if (file_exists(MOLAJO_LIBRARY.'/helpers/file.php')) {
        JLoader::register('MolajoFileHelper', MOLAJO_LIBRARY.'/helpers/file.php');
    } else {
        JError::raiseNotice(500, MolajoText::_('MOLAJO_OVERRIDE_CREATE_MISSING_CLASS_FILE'.' '.'MolajoFileHelper'));
        return;
    }
}
$filehelper = new MolajoFileHelper();

/** language */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/language/language.php', 'JLanguage');

/** text */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/route.php', 'MolajoRoute');

/** registry */
JLoader::register('MolajoRegistryFormat', MOLAJO_LIBRARY.'/utilities/format.php');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/utilities/registry.php', 'MolajoRegistry');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/authentication.php', 'MolajoAuthentication');

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
/** Session */
jimport('joomla.session.session');

/** Application */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/categories.php', 'MolajoCategories');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/categories.php', 'MolajoCategoriesHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/application.php', 'MolajoApplication');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/exception.php', 'MolajoException');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/pathway.php', 'MolajoPathway');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/utility.php', 'MolajoUtility');

if (file_exists(MOLAJO_PATH_BASE.'/includes/helper.php')) {
    require_once MOLAJO_PATH_BASE.'/includes/helper.php';
}

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/base/node.php', 'JNode');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/base/tree.php', 'JTree');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/application.php', 'MolajoApplicationHelper');

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/cli.php', 'JCli');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/cli/daemon.php', 'JDaemon');

$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/component.php', 'MolajoComponentHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/module.php', 'MolajoModuleHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/language.php', 'MolajoLanguageHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/menu.php', 'MolajoMenu');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/router.php', 'MolajoRouter');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/toolbar.php', 'MolajoToolbarHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/user.php', 'MolajoUserHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/user/user.php', 'MolajoUser');

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/environment/uri.php', 'JURI');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/environment/browser.php', 'JBrowser');

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/event/event.php', 'JEvent');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/event/dispatcher.php', 'JDispatcher');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filter/filterinput.php', 'JFilterInput');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filter/filteroutput.php', 'JFilterOutput');

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/simplecrypt.php', 'JSimplecrypt');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/string.php', 'JString');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/arrayhelper.php', 'JArrayHelper');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/xmlelement.php', 'JXMLElement');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/simplexml.php', 'JSimpleXML');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/buffer.php', 'JBuffer');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/date.php', 'JDate');

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

/** Client */
$files = JFolder::files(JOOMLA_LIBRARY.'/client', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/client/'.$file, 'JClientHelper');
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/client/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Database and Table */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/database.php', 'JDatabase');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/databaseexception.php', 'DatabaseException');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/databasequery.php', 'JDatabaseQueryElement');
$filehelper->requireClassFile(MOLAJO_LIBRARY_TABLES.'/table.php', 'MolajoTable');
$filehelper->requireClassFile(MOLAJO_LIBRARY_TABLES.'/tablenested.php', 'MolajoTableNested');

$files = JFolder::files(MOLAJO_LIBRARY_TABLES, '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'table.php' || $file == 'tablenested.php') {
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_TABLES.'/'.$file, 'MolajoTable'.ucfirst(substr($file, 0, strpos($file, '.'))));
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

/** Utilities */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/mail.php', 'MolajoMailHelper');
$filehelper->requireClassFile(JPATH_PLATFORM.'/phpmailer/phpmailer.php', 'PHPMailer');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/utilities/mail.php', 'MolajoMail');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/utilities/date.php', 'MolajoDate');
$files = JFolder::files(JOOMLA_LIBRARY.'/utilities', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'date') {
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

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

/**
 *  Installer still using JFramework for MVC/JForm/JHTML
 */
$filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/formfield.php', 'MolajoFormField');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/formrule.php', 'MolajoFormRule');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/helper.php', 'MolajoFormHelper');
$filehelper->requireClassFile(MOLAJO_LIBRARY_FORM.'/form.php', 'MolajoForm');

/** Plugins */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/application/plugin.php', 'MolajoPlugin');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/helpers/plugin.php', 'MolajoPluginHelper');

/** Mail */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/mail/mail.php', 'JMail');
 
/** Document */
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/document.php', 'MolajoDocument');
$filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/renderer.php', 'MolajoDocumentRenderer');

$format = JRequest::getCmd('format', 'html');
if ($format == 'error' || $format == 'feed' || $format == 'raw') {
    $includeFormat = $format;
} else {
    $includeFormat = 'html';
}
$formatClass = 'MolajoDocument'.ucfirst($includeFormat);
if (class_exists($formatClass)) {
} else {
    $path = MOLAJO_LIBRARY.'/document/'.$includeFormat.'/'.$includeFormat.'.php';
    $formatClass = 'MolajoDocument'.ucfirst($includeFormat);
    $filehelper->requireClassFile(MOLAJO_LIBRARY.'/document/'.$includeFormat.'/'.$includeFormat.'.php', $formatClass);
}
/** MolajoField */
$filehelper->requireClassFile(MOLAJO_LIBRARY_FIELDS.'/field.php', 'MolajoField');

/** Controller */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/controller.php', 'JController');
$files = JFolder::files(MOLAJO_LIBRARY_CONTROLLERS, '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'controller.php') {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_CONTROLLERS.'/controller.php', 'MolajoController');
    } else {
        $filehelper->requireClassFile(MOLAJO_LIBRARY_CONTROLLERS.'/'.$file, 'MolajoController'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
/** Fields do not load so that component can override fields, attributes and fieldtypes */

/** Models */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/model.php', 'JModel');
$files = JFolder::files(MOLAJO_LIBRARY_MODELS, '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_MODELS.'/'.$file, 'MolajoModel'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Model-Elements */
$files = JFolder::files(MOLAJO_LIBRARY_MODELS.'/elements', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(MOLAJO_LIBRARY_MODELS.'/elements/'.$file, 'MolajoElement'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Views */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/view.php', 'JView');
$filehelper->requireClassFile(MOLAJO_LIBRARY_VIEWS.'/view.php', 'MolajoView');
$files = JFolder::files(MOLAJO_LIBRARY_VIEWS, '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'layout.php' || $file == 'view.php') {
    } else {
        if (strpos($file, $includeFormat)) {
            $filehelper->requireClassFile(MOLAJO_LIBRARY_VIEWS.'/'.$file, 'MolajoView'.ucfirst(substr($file, 0, strpos($file, '.'))));
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

/** Views */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/view.php', 'JView');
$filehelper->requireClassFile(MOLAJO_LIBRARY_VIEWS.'/view.php', 'MolajoView');
$files = JFolder::files(MOLAJO_LIBRARY_VIEWS, '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'layout.php' || $file == 'view.php') {
    } else {
        if (strpos($file, $includeFormat)) {
            $filehelper->requireClassFile(MOLAJO_LIBRARY_VIEWS.'/'.$file, 'MolajoView'.ucfirst(substr($file, 0, strpos($file, '.'))));
        }
    }
}

/** Replace */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/html/html.php', 'JHtml');