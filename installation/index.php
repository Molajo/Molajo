<?php
/**
 * @package     Molajo
 * @subpackage  Bootstrap
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
define('MOLAJO_APPLICATION', basename(dirname(__FILE__)));
define('DS', DIRECTORY_SEPARATOR);

$root = '';
$parts = explode(DS, dirname(__FILE__));
for ($i = 0; $i < count($parts) - 1; $i++) {
    if ($i == 0) {
    } else {
        $root .= DS;
    }
    $root .= $parts[$i];
}
require_once $root . '/index.php';