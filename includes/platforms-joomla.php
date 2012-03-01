<?php
/**
 * @package     Molajo
 * @subpackage  Load Joomla Framework
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (defined('JPATH_PLATFORM')) {
} else {
    define('JPATH_PLATFORM', PLATFORMS . '/jplatform/');
}

require_once MOLAJO_APPLICATIONS . '/helpers/load.php';
$load = new LoadHelper();

require_once JPATH_PLATFORM . '/platform.php';
require_once JPATH_PLATFORM . '/loader.php';
require_once JPATH_PLATFORM . '/joomla' . '/base/object.php';
require_once JPATH_PLATFORM . '/joomla' . '/registry/registry.php';

$load->requireClassFile(MOLAJO_APPLICATIONS . '/base/base.php', 'MolajoBase');
$load->requireClassFile(MOLAJO_APPLICATIONS_MVC . '/controllers/controller.php', 'Controller');

require_once MOLAJO_APPLICATIONS . '/services/language.php';

$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/filesystem/path.php', 'JPath');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/filesystem/file.php', 'JFile');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/filesystem/folder.php', 'JFolder');

/**
 *  Client
 *
 */
$files = JFolder::files(JPATH_PLATFORM . '/joomla' . '/client', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'ftp.php') {
        /** babs cannot run this require statement - not sure why yet */
    } else if ($file == 'helper.php') {
        $load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/client/' . $file, 'JClientHelper');
    } else {
        $load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/client/' . $file, 'J' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Database
 */
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database.php', 'JDatabase');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/query.php', 'JDatabaseQueryElement');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/exception.php', 'JDatabaseException');

$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/mysql.php', 'JDatabaseMySQL');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/mysqli.php', 'JDatabaseMySQLi');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/sqlsrv.php', 'JDatabaseSQLSrv');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/sqlazure.php', 'JDatabaseSQLAzure');

$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/mysqlexporter.php', 'JDatabaseExporterMySQL');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/mysqliexporter.php', 'JDatabaseExporterMySQLi');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/mysqlimporter.php', 'JDatabaseImporterMySQL');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/database/mysqliimporter.php', 'JDatabaseImporterMySQLi');

$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/query/mysql.php', 'JDatabaseQueryMysql');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/query/mysqli.php', 'JDatabaseQueryMysqli');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/query/sqlsrv.php', 'JDatabaseQuerySqlsrv');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/database/query/sqlazure.php', 'JDatabaseQuerySqlazure');

/**
 *  Filesystem (continued)
 */

$files = JFolder::files(JPATH_PLATFORM . '/joomla' . '/filesystem', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
    } elseif ($file == 'path.php' || $file == 'file.php' || $file == 'folder.php') {
    } elseif ($file == 'stream.php' || $file == 'patcher.php') {
    } else {
        $load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/filesystem/' . $file, 'J' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(JPATH_PLATFORM . '/joomla' . '/filesystem/streams', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/filesystem/streams/' . $file, 'JStream' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(JPATH_PLATFORM . '/joomla' . '/filesystem/support', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/filesystem/support/' . $file, 'J' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Registry
 */
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/registry/format.php', 'JRegistryFormat');
$files = JFolder::files(JPATH_PLATFORM . '/joomla' . '/registry/format', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/registry/format/' . $file, 'JRegistryFormat' . strtoupper(substr($file, 0, strpos($file, '.'))));
}

/**
 *  String
 */
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/string/string.php', 'JString');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/string/stringnormalize.php', 'JStringNormalize');

/**
 *  Utilities
 */
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/utilities/arrayhelper.php', 'JArrayHelper');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/utilities/buffer.php', 'JBuffer');
$load->requireClassFile(JPATH_PLATFORM . '/joomla' . '/utilities/date.php', 'JDate');

/**
 *  PHPMailer
 */
$load->requireClassFile(PLATFORMS . '/jplatform/phpmailer/phpmailer.php', 'PHPMailer');
