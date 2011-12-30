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
<th width="10%">
    <?php echo MolajoHTML::_('grid.sort', 'MOLAJO_FIELD_ORDERING_LABEL', 'a.ordering', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
    <?php if ($this->saveOrder) : ?>
    <?php echo MolajoHTML::_('grid.order', $this->row, 'filesave.png', $this->state->get('request.DefaultView') . '.saveorder'); ?>
    <?php endif; ?>
</th>