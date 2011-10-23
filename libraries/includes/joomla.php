<?php
/**
 * @package     Molajo
 * @subpackage  Load Framework
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 * File Subsystem
 */
$filehelper = new MolajoFileHelper();

$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/path.php', 'JPath');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/file.php', 'JFile');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/folder.php', 'JFolder');
$files = JFolder::files(JOOMLA_LIBRARY.'/filesystem', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/'.$file, 'JFilesystemHelper');
    } elseif ($file == 'path.php' || $file == 'file.php' || $file == 'folder.php') {
    } elseif ($file == 'stream.php') { // todo: amy figure out why stream won't load
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Access - not used
 */

/**
 *  Base
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/base/object.php', 'JObject');
$files = JFolder::files(JOOMLA_LIBRARY.'/base', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'adapter.php' || $file == 'adapterinstance.php' || $file == 'object.php' ) {
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/base/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Application
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/controller.php', 'JController');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/model.php', 'JModel');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/component/view.php', 'JView');

/**
 *  Cache
 */
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

/**
 *  Client
 */
$files = JFolder::files(JOOMLA_LIBRARY.'/client', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/client/'.$file, 'JClientHelper');
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/client/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Database
 */
JLoader::register('JDatabaseQueryMySQL', JOOMLA_LIBRARY.'/database/database/mysqlquery.php');
JLoader::register('JDatabaseExporterMySQL', JOOMLA_LIBRARY.'/database/database/mysqlexporter.php');
JLoader::register('JDatabaseImporterMySQL', JOOMLA_LIBRARY.'/database/database/mysqlimporter.php');
JLoader::register('JDatabaseMySQL', JOOMLA_LIBRARY.'/database/database/mysql.php');

JLoader::register('JDatabaseQueryMySQLi', JOOMLA_LIBRARY.'/database/database/mysqliquery.php');
JLoader::register('JDatabaseExporterMySQLi', JOOMLA_LIBRARY.'/database/database/mysqlexporter.php');
JLoader::register('JDatabaseImporterMySQLi', JOOMLA_LIBRARY.'/database/database/mysqliimporter.php');
JLoader::register('JDatabaseMySQLi', JOOMLA_LIBRARY.'/database/database/mysqli.php');

JLoader::register('JDatabaseQuerySQLAzure', JOOMLA_LIBRARY.'/database/database/sqlazurequery.php');
JLoader::register('JDatabaseSQLAzure', JOOMLA_LIBRARY.'/database/database/sqlazure.php');

JLoader::register('JDatabaseQuerySQLSrv', JOOMLA_LIBRARY.'/database/database/sqlsrvquery.php');
JLoader::register('JDatabaseSQLSrv', JOOMLA_LIBRARY.'/database/database/sqlsrv.php');

JLoader::register('JDatabaseInterface', JOOMLA_LIBRARY.'/database/database.php');
JLoader::register('JDatabase', JOOMLA_LIBRARY.'/database/database.php');
JLoader::register('JDatabaseQueryElement', JOOMLA_LIBRARY.'/database/databasequery.php');
JLoader::register('JDatabaseQuery', JOOMLA_LIBRARY.'/database/databasequery.php');

/**
 *  Document - not used
 */

/**
 *  Environment
 */
$files = JFolder::files(JOOMLA_LIBRARY.'/environment', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/environment/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Error - JError deprecated; Exception classes loaded in Molajo; Log moved
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/error/error.php', 'JError');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/error/profiler.php', 'JProfiler');

/**
 *  Exceptions
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/error/exception.php', 'JException');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/application/applicationexception.php', 'ApplicationException');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/database/databaseexception.php', 'JDatabaseException');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/log/logexception.php', 'Logexception');

/**
 *  Event
 */
$files = JFolder::files(JOOMLA_LIBRARY.'/event', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/event/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Filesystem (continued)
 */
$files = JFolder::files(JOOMLA_LIBRARY.'/filesystem/archive', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/archive/'.$file, 'JArchive'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(JOOMLA_LIBRARY.'/filesystem/streams', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/streams/'.$file, 'JStream'.ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(JOOMLA_LIBRARY.'/filesystem/support', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/filesystem/support/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Filter
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filter/filterinput.php', 'JFilterInput');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/filter/filteroutput.php', 'JFilterOutput');

/**
 *  Form - not used
 */

/**
 *  HTML - not used
 */

/**
 *  Installer - not used
 */

/**
 *  Language (JHelp and JLanguageHelper not used)
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/language/language.php', 'JLanguage');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/language/latin_transliterate.php', 'JLanguageTransliterate');

/**
 *  Log
 */
$files = JFolder::files(JOOMLA_LIBRARY.'/log', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'logexception.php') {
    } else {
        $filehelper->requireClassFile(JOOMLA_LIBRARY.'/log/'.$file, 'J'.ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(JOOMLA_LIBRARY.'/log/loggers', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/log/loggers/'.$file, 'JLogger'.ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Mail - not used
 */

/**
 *  Plugin - not used
 */

/**
 *  Registry
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/registry/registry.php', 'JRegistry');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/registry/format.php', 'JRegistryFormat');
$files = JFolder::files(JOOMLA_LIBRARY.'/registry/format', '\.php$', false, false);
foreach ($files as $file) {
    $filehelper->requireClassFile(JOOMLA_LIBRARY.'/registry/format/'.$file, 'JRegistryFormat'.strtoupper(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Session - not used
 */

/**
 *  String
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/string/string.php', 'JString');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/string/stringnormalize.php', 'JStringNormalize');

/**
 *  Updater - not used
 */

/**
 *  User - not used
 */

/**
 *  Utilities
 *      JSimpleCrypt, JSimpleXML, JSimpleXMLElement, JUtility and JXMLElement deprecated
 *      String moved
 */
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/arrayhelper.php', 'JArrayHelper');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/buffer.php', 'JBuffer');
$filehelper->requireClassFile(JOOMLA_LIBRARY.'/utilities/date.php', 'JDate');

/**
 *  PHPMailer
 */
$filehelper->requireClassFile(LIBRARIES.'jplatform/phpmailer/phpmailer.php', 'PHPMailer');

