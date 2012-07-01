<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;

$columnCount = Services::Registry()->get('Trigger', 'GridTableColumns');
?>
</tbody>
	<tfoot>
		<tr>
			<td colspan="<?php echo ((int) $columnCount + 1); ?>">
				<include:template name=Admingridpagination value=GridPagination/>
			</td>
		</tr>
	</tfoot>
</table>
