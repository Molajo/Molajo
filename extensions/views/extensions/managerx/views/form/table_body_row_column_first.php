<?php
/**
 * @version     $id: view
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
?>
<td class="center nowrap" width="2%" valign="top">
    <?php if (($this->row->canEdit && ((int)$this->row->state < 2))
              || $this->row->canCheckin || $this->row->canEditstate || $this->row->canDelete
) {
    echo MolajoHTML::_('grid.id', $this->row->rowCount, $this->row->id);
} ?>
</td>