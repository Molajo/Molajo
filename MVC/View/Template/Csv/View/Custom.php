<?php
/**
 * Csv Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('MOLAJO') or die;
$csv = "";
foreach ($this->query_results as $row) {
    $csv .= join(",", $row) . "\n";
}
echo $csv;
