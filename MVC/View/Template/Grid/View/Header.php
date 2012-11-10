<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$nowrap = '';
$checked = '';
$rowCount = Services::Registry()->get('Plugindata', 'GridTableRows'); ?>
<table class="twelve responsive">
    <thead>
    <tr>
        <?php
        $count = 1;
        $columnArray = Services::Registry()->get('Plugindata', 'GridTableColumns');
        foreach ($columnArray as $column) {
            $extraClass = '';
            $nowrap = '';
            if ($count == 1) {
                $extraClass .= 'first';
                $nowrap = ' nowrap';
            }
            if ($count == count($columnArray)) {
                $extraClass .= 'last';
            }
            if ($extraClass == '') {
            } else {
                $extraClass = ' class="' . trim($extraClass) . '"';
            }
            ?>
            <th<?php echo $extraClass . $nowrap; ?>><span><?php echo Services::Language()->translate('GRID_' . strtoupper($column) . '_COLUMN_HEADING'); ?></span></th>
            <?php
            $count++;
        } ?>
        <th width="1%">
            <input type="checkbox" class="checkall">
        </th>
    </tr>
    </thead>
    <tbody>
