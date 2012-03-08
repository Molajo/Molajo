<?php
/**
 * @package     Molajo
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

if (defined('JPATH_SITE')) {
} else {
    define('JPATH_SITE', MOLAJO_BASE_FOLDER);
}
if (defined('JPATH_PLATFORM')) {
} else {
    define('JPATH_PLATFORM', VENDOR . '/Joomla');
}

require_once JPATH_PLATFORM . '/platform.php';
require_once JPATH_PLATFORM . '/loader.php';
//use Molajo\Application\Helper\LoadHelper;
//require_once MOLAJO_APPLICATIONS . '/Helper/FileloadHelper.php';
//$load = new FileloadHelper();

//require_once JPATH_PLATFORM . '/base/object.php';

// require_once MOLAJO_APPLICATIONS . '/services/language.php';
$load->requireClassFile(JPATH_PLATFORM . '/filesystem/path.php', 'JPath');
$load->requireClassFile(JPATH_PLATFORM . '/filesystem/file.php', 'JFile');
$load->requireClassFile(JPATH_PLATFORM . '/filesystem/folder.php', 'JFolder');

/** Client */
$files = JFolder::files(JPATH_PLATFORM . '/client', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'ftp.php') {
        /** babs cannot run this require statement - not sure why yet */
    } else if ($file == 'helper.php') {
        $load->requireClassFile(JPATH_PLATFORM . '/client/' . $file, 'JClientHelper');
    } else {
        $load->requireClassFile(JPATH_PLATFORM . '/client/' . $file, 'J' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/** Database */
$load->requireClassFile(JPATH_PLATFORM . '/database/database.php', 'JDatabase');
$load->requireClassFile(JPATH_PLATFORM . '/database/query.php', 'JDatabaseQueryElement');
$load->requireClassFile(JPATH_PLATFORM . '/database/exception.php', 'JDatabaseException');

$load->requireClassFile(JPATH_PLATFORM . '/database/database/mysql.php', 'JDatabaseMySql');
$load->requireClassFile(JPATH_PLATFORM . '/database/database/mysqli.php', 'JDatabaseMySqli');
$load->requireClassFile(JPATH_PLATFORM . '/database/database/sqlsrv.php', 'JDatabaseSQLSrv');
$load->requireClassFile(JPATH_PLATFORM . '/database/database/sqlazure.php', 'JDatabaseSQLAzure');

$load->requireClassFile(JPATH_PLATFORM . '/database/database/mysqlexporter.php', 'JDatabaseExporterMySql');
$load->requireClassFile(JPATH_PLATFORM . '/database/database/mysqliexporter.php', 'JDatabaseExporterMySqli');
$load->requireClassFile(JPATH_PLATFORM . '/database/database/mysqlimporter.php', 'JDatabaseImporterMySQL');
$load->requireClassFile(JPATH_PLATFORM . '/database/database/mysqliimporter.php', 'JDatabaseImporterMySQLi');

$load->requireClassFile(JPATH_PLATFORM . '/database/query/mysql.php', 'JDatabaseQueryMysql');
$load->requireClassFile(JPATH_PLATFORM . '/database/query/mysqli.php', 'JDatabaseQueryMysqli');
$load->requireClassFile(JPATH_PLATFORM . '/database/query/sqlsrv.php', 'JDatabaseQuerySqlsrv');
$load->requireClassFile(JPATH_PLATFORM . '/database/query/sqlazure.php', 'JDatabaseQuerySqlazure');

/** Filesystem */
$files = JFolder::files(JPATH_PLATFORM . '/filesystem', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
    } elseif ($file == 'path.php' || $file == 'file.php' || $file == 'folder.php') {
    } elseif ($file == 'stream.php' || $file == 'patcher.php') {
    } else {
        $load->requireClassFile(JPATH_PLATFORM . '/filesystem/' . $file, 'J' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}
$files = JFolder::files(JPATH_PLATFORM . '/filesystem/streams', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(JPATH_PLATFORM . '/filesystem/streams/' . $file, 'JStream' . ucfirst(substr($file, 0, strpos($file, '.'))));
}
$files = JFolder::files(JPATH_PLATFORM . '/filesystem/support', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(JPATH_PLATFORM . '/filesystem/support/' . $file, 'J' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/** Registry */
$load->requireClassFile(JPATH_PLATFORM . '/registry/format.php', 'JRegistryFormat');
$files = JFolder::files(JPATH_PLATFORM . '/registry/format', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(JPATH_PLATFORM . '/registry/format/' . $file, 'JRegistryFormat' . strtoupper(substr($file, 0, strpos($file, '.'))));
}

/** String */
$load->requireClassFile(JPATH_PLATFORM . '/string/string.php', 'JString');
$load->requireClassFile(JPATH_PLATFORM . '/string/stringnormalize.php', 'JStringNormalize');

/**
 *  Utilities
 */
$load->requireClassFile(JPATH_PLATFORM . '/utilities/arrayhelper.php', 'JArrayHelper');
$load->requireClassFile(JPATH_PLATFORM . '/utilities/buffer.php', 'JBuffer');
$load->requireClassFile(JPATH_PLATFORM . '/utilities/date.php', 'JDate');





abstract class JError
{
    static $legacy = false;
}

class Registry extends JRegistry
{
}
