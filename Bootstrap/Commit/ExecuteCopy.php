<?php
/**
 * Execute Copy
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

$exclude_folders = array('.git');

require_once $base_path . '/Molajo/Bootstrap/Commit/Copy.php';
$class = 'Molajo\\Copy';
$commit = new $class ($base_path, $source_plugin, $target_plugin, $exclude_folders);
$commit->process();
