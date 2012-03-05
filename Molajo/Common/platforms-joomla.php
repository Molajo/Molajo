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
use Molajo\Application\Helper\LoadHelper;
require_once MOLAJO_APPLICATIONS . '/Helper/FileloadHelper.php';
//$load = new FileloadHelper();

//require_once JPATH_PLATFORM . '/base/object.php';

// require_once MOLAJO_APPLICATIONS . '/services/language.php';
require_once JPATH_PLATFORM . '/filesystem/path.php';
require_once JPATH_PLATFORM . '/filesystem/file.php';
require_once JPATH_PLATFORM . '/filesystem/folder.php';

/** Client */
$files = JFolder::files(JPATH_PLATFORM . '/client', '\.php$', false, false);
foreach ($files as $file) {
        require_once JPATH_PLATFORM . '/client/' . $file;
}

/** Database */
require_once JPATH_PLATFORM . '/database/database.php';
require_once JPATH_PLATFORM . '/database/query.php';
require_once JPATH_PLATFORM . '/database/exception.php';

require_once JPATH_PLATFORM . '/database/database/mysql.php';
require_once JPATH_PLATFORM . '/database/database/mysqli.php';
require_once JPATH_PLATFORM . '/database/database/sqlsrv.php';
require_once JPATH_PLATFORM . '/database/database/sqlazure.php';

require_once JPATH_PLATFORM . '/database/database/mysqlexporter.php';
require_once JPATH_PLATFORM . '/database/database/mysqliexporter.php';
require_once JPATH_PLATFORM . '/database/database/mysqlimporter.php';
require_once JPATH_PLATFORM . '/database/database/mysqliimporter.php';

require_once JPATH_PLATFORM . '/database/query/mysql.php';
require_once JPATH_PLATFORM . '/database/query/mysqli.php';
require_once JPATH_PLATFORM . '/database/query/sqlsrv.php';
require_once JPATH_PLATFORM . '/database/query/sqlazure.php';

/** Filesystem */
$files = JFolder::files(JPATH_PLATFORM . '/filesystem', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'helper.php') {
    } elseif ($file == 'path.php' || $file == 'file.php' || $file == 'folder.php') {
    } elseif ($file == 'stream.php' || $file == 'patcher.php') {
    } else {
        require_once JPATH_PLATFORM . '/filesystem/' . $file;
    }
}
//$files = JFolder::files(JPATH_PLATFORM . '/filesystem/streams', '\.php$', false, false);
//foreach ($files as $file) {
//    require_once JPATH_PLATFORM . '/filesystem/streams/' . $file, 'JStream' . ucfirst(substr($file, 0, strpos($file, '.'))));
//}
//$files = JFolder::files(JPATH_PLATFORM . '/filesystem/support', '\.php$', false, false);
//foreach ($files as $file) {
//    require_once JPATH_PLATFORM . '/filesystem/support/' . $file, 'J' . ucfirst(substr($file, 0, strpos($file, '.'))));
//}

/** Registry */
require_once JPATH_PLATFORM . '/registry/format.php';
$files = JFolder::files(JPATH_PLATFORM . '/registry/format');
foreach ($files as $file) {
    require_once JPATH_PLATFORM . '/registry/format/' . $file;
}

/** String */
require_once JPATH_PLATFORM . '/string/string.php';
require_once JPATH_PLATFORM . '/string/stringnormalize.php';

/**
 *  Utilities
 */
require_once JPATH_PLATFORM . '/utilities/arrayhelper.php';
require_once JPATH_PLATFORM . '/utilities/buffer.php';
require_once JPATH_PLATFORM . '/utilities/date.php';



return;

abstract class JError
{
    static $legacy = false;
}

class Registry extends JRegistry
{
}
