<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$columnCount = Services::Registry()->get('Triggerdata', 'AdminGridTableColumns');
?>
</tbody>
<tfoot>
<tr>
    <td colspan="<?php echo ((int) $columnCount + 1); ?>">
        <include:template name=Admingridpagination value=AdminGridPagination/>
    </td>
</tr>
</tfoot>
</table>
