<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
use Molajo\Service\Services;
defined('MOLAJO') or die;
$columns = Services::Registry()->get('Trigger', 'GridTableColumns');
$columnCount = count($columns) + 1; ?>
</tbody>
	<tfoot>
		<tr>
			<td colspan="<?php echo $columnCount; ?>">
				<include:template name=Admingridpagination wrap=None value=GridPagination/>
			</td>
		</tr>
	</tfoot>
</table>
