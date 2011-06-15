<?php
/**
 * @version     $id: layout
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<tfoot>
    <tr>
        <td colspan="<?php echo $this->tempColumnCount; ?>">
            <?php echo $this->pagination->getListFooter(); ?>
        </td>
    </tr>
</tfoot>