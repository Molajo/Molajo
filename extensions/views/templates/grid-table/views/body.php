<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$result = $this->row->row_count % 2;
if ($result == 0) {
    $rowClass = ' class="even"';
} else {
    $rowClass = ' class="odd"';
}
?>
        <tr<?php echo $rowClass ?>>
<?php
$columnCount = 1;
$columns = Molajo::Request()->parameters->get('columns');
$columnArray = explode(',', $columns);
foreach ($columnArray as $column) {
    $extraClass = '';
    if ($columnCount == 1) {
        $extraClass .= 'first';
    }
    if ($columnCount == count($columnArray)) {
        $extraClass .= 'last';
    }
    if ($extraClass == '') {
    } else {
        $extraClass = ' class="' . trim($extraClass) . '"';
    }
?>
            <td<?php echo $extraClass; ?>><?php echo $this->row->$column; ?></td>
<?php
    $columnCount++;
} ?>
        </tr>
