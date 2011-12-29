<?php
/**
 * @version     $this->row->rowCountd: layout
 * @package     Molajo
 * @subpackage  Multiple View
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
$defaultView = $this->state->get('request.DefaultView'); ?>

<td class="order">
    <?php if ($this->row->canEditstate) : ?>
    <?php if ($this->saveOrder) : ?>
        <?php if ($this->state->get('list.direction') == 'asc') : ?>
            <span><?php echo $this->pagination->orderUpIcon($this->row->rowCount, ($this->row->category_id == @$this->row[$this->row->rowCount - 1]->category_id), $this->state->get('request.DefaultView') . '.orderup', 'MOLAJO_HTML_MOVE_UP', $this->ordering); ?></span>
            <span><?php echo $this->pagination->orderDownIcon($this->row->rowCount, $this->pagination->total, ($this->row->category_id == @$this->row[$this->row->rowCount + 1]->category_id), $this->state->get('request.DefaultView') . '.orderdown', 'MOLAJO_HTML_MOVE_DOWN', $this->ordering); ?></span>
            <?php elseif ($this->state->get('list.direction') == 'desc') : ?>
            <span><?php echo $this->pagination->orderUpIcon($this->row->rowCount, ($this->row->category_id == @$this->row[$this->row->rowCount - 1]->category_id), $this->state->get('request.DefaultView') . '.orderdown', 'MOLAJO_HTML_MOVE_UP', $this->ordering); ?></span>
            <span><?php echo $this->pagination->orderDownIcon($this->row->rowCount, $this->pagination->total, ($this->row->category_id == @$this->row[$this->row->rowCount + 1]->category_id), $this->state->get('request.DefaultView') . '.orderup', 'MOLAJO_HTML_MOVE_DOWN', $this->ordering); ?></span>
            <?php endif; ?>
        <?php endif; ?>
    <?php $disabled = $this->saveOrder ? '' : 'disabled="disabled"'; ?>
    <input type="text" name="order[]" size="5" value="<?php echo $this->row->ordering; ?>" <?php echo $disabled ?>
           class="text-area-order"/>
    <?php else : ?>
    <?php echo $this->row->ordering; ?>
    <?php endif; ?>
</td>