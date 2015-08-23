<?php
/**
 * Themes
 *
 * @package    Molajo
 * @copyright  2014-2015 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */
$base_path   = substr(__DIR__, 0, strlen(__DIR__) - strlen('/Molajo/Bootstrap/Commit'));
$source_path = '/Molajo/Source/Themes/';
$target_path = '/git/MolajoThemes/';

foreach (scandir($base_path . $source_path) as $folder) {

    if (substr($folder, 0, 1) === '.') {
    } else {
        $source_plugin = $source_path . $folder;
        $target_plugin = $target_path . $folder;
        include $base_path . '/Molajo/Bootstrap/Commit/ExecuteCopy.php';
    }
}

echo 'Run ' . $base_path . $target_path . 'github.txt contents within folder: ' . $base_path . $target_path . chr(10);
