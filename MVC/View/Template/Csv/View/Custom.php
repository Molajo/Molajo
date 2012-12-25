<?php

/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;
$csv = "";
foreach ($this->query_results as $row) {
    $csv .= join(",", $row) . "\n";
}
echo $csv;
