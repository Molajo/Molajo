<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<tr class="row<?php echo $i % 2; ?>">
<?php
$count = 0;
foreach ($columnArray as $column) {
    ?>
    <td align="center">
        <?php echo $this->row->$column; ?>
    </td>
<?php $count++;
} ?>
</tr>
