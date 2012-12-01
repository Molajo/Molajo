<?php

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$csv = "";
foreach ($this->query_results as $row) {
    $csv .= join(",", $row) . "\n";
}
echo $csv;
