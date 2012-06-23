<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
?>
<tr<?php echo $this->row->grid_row_class; ?>><?php
$columnCount = 1;
$columnArray = Services::Registry()->get('Trigger', 'GridTableColumns');
foreach ($columnArray as $column) {	?>
	<td<?php echo $this->row->css_class; ?>><?php
	if ($column == 'title') {
		echo '<a href="' . $this->row->catalog_id_url . '">';
	}
	echo $this->row->$column;
	if ($column == 'title') {
		echo '</a>';
	} ?>
	</td><?php
	$columnCount++;
	}
?>
	<td class="center last">
		<input type=checkbox value="<?php echo $checked; ?>">
	</td>
</tr>
