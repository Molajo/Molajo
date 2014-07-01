<?php
/**
 * Creates Site
 *
 * @package    Molajo
 * @copyright  2014 Amy Stephen. All rights reserved.
 * @license    http://www.opensource.org/licenses/mit-license.html MIT License
 */

$file
    = json_decode(
    file_get_contents('sites.json'),
    true
);

/** Insert New Row */
$count = count($file['sites']['site']) - 1;
$i     = $file['sites']['site'][$count]['id'] + 1;

$file['sites']['site'][]
    = array(
    "id"             => (string)$i,
    "name"           => 'Site ' . $i,
    "site_base_url"  => 'site' . (string)$i,
    "site_base_path" => '/Site/' . $i
);

/** Delete Row */
$delete = 2;
$i      = 0;
foreach ($file['sites']['site'] as $id => $site) {
    if ($site['id'] == $delete) {
        unset($file['sites']['site'][$i]);
    }
    $i++;
}

/** Update Row */
$delete = 2;
$i      = 0;
foreach ($file['sites']['site'] as $id => $site) {
    if ($site['id'] == $delete) {
        unset($file['sites']['site'][$i]);
    }
    $i++;
}

/** Save file */
$output_file = fopen('data_out.json', 'w')
or die('Error opening output file');

if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
    fwrite($output_file, json_encode($file, JSON_UNESCAPED_UNICODE));
} else {
    fwrite($output_file, json_encode($file));
}
fclose($output_file);

echo '<pre>';
var_dump($file);
echo '</pre>';
