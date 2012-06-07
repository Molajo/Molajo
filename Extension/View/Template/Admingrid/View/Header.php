<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
echo '<pre>';
var_dump(Services::Registry()->get('Trigger', '*'));
echo '</pre>';

defined('MOLAJO') or die; ?>
<table class="gridlist">
    <thead>
    <tr>
        <?php
        $count = 1;
		$columnArray = Services::Registry()->get('Trigger', 'GridTableColumns');
        foreach ($columnArray as $column) {
			echo $column;
            $extraClass = '';
            if ($count == 1) {
                $extraClass .= 'first';
            }
            if ($count == count($columnArray)) {
                $extraClass .= 'last';
            }
            if ($extraClass == '') {
            } else {
                $extraClass = ' class="' . trim($extraClass) . '"';
            }
            ?>
            <th<?php echo $extraClass; ?>><?php echo Services::Language()->translate('GRID_' . strtoupper($column) . '_COLUMN_HEADING'); ?></th>
            <?php
            $count++;
        } ?>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="<?php echo $count + 1; ?>"><?php //echo $this->pagination->getListFooter(); ?></td>
    </tr>
    </tfoot>
    <tbody>
