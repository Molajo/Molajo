<?php
/**
 * @package     Molajo
 * @subpackage  Load Molajo Framework
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

/**
 *  File Helper
 */
$load = new MolajoLoadHelper();

/**
 *  Exceptions - already loaded
 */

/**
 *  Session
 */
$load->requireClassFile(PLATFORM_MOLAJO . '/session/session.php', 'MolajoSession');
$load->requireClassFile(PLATFORM_MOLAJO . '/session/storage.php', 'MolajoSessionStorage');
$files = JFolder::files(PLATFORM_MOLAJO . '/session/storage', '\.php$', false, false);
foreach ($files as $file) {
    $load->requireClassFile(PLATFORM_MOLAJO . '/session/storage/' . $file, 'MolajoSessionStorage' . ucfirst(substr($file, 0, strpos($file, '.'))));
}

/**
 *  Utilities
 */
$files = JFolder::files(PLATFORM_MOLAJO . '/utilities', '\.php$', false, false);
foreach ($files as $file) {
    if ($file == 'LoremIpsum.class.php') {
        $load->requireClassFile(PLATFORM_MOLAJO . '/utilities/' . $file, 'LoremIpsumGenerator');
    } else {
        $load->requireClassFile(PLATFORM_MOLAJO . '/utilities/' . $file, 'Molajo' . ucfirst(substr($file, 0, strpos($file, '.'))));
    }
}

/**
 *  Debug
 */
$load->requireClassFile(PLATFORM_MOLAJO . '/debug/PhpConsole.php', 'PhpConsole');
PhpConsole::start(true, true, PLATFORM_MOLAJO . '/debug');

