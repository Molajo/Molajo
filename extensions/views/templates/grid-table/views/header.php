<?php
/**
 * @package     Molajo
 * @subpackage  Views
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<table class="gridlist" width="100%">
	<thead>
		<tr>
<?php
$count = 0;
$columns = Molajo::Request()->parameters->get('columns');
$columnArray = explode(',', $columns);
foreach ($columnArray as $column) {
    ?>
    <th align="center">
        <?php echo Services::Language()->translate('GRID_'.strtoupper($column).'_COLUMN_HEADING'); ?>
    </th>
<?php $count++;
} ?>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="<?php echo $count + 1; ?>">
				<?php //echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
