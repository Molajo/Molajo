<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$rowCount = Services::Registry()->get('Trigger', 'GridTableRows') % 2;
if ($rowCount == 0) {
    $rowClass = ' class="even"';
} else {
    $rowClass = ' class="odd"';
}
?>
	<tr<?php echo $rowClass ?>>
		<?php
		$columnCount = 1;
		$columnArray = Services::Registry()->get('Trigger', 'GridTableColumns');
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
			<td<?php echo $extraClass; ?>>
				<?php echo $this->row->$column; ?>
			</td>
			<?php
			$columnCount++;
		} ?>
	</tr>
