<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2012 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<tr>
    <th scope="row">
        <?php if ($this->row->checked_out) : ?>
        <?php echo MolajoHTML::_('jgrid.checkedout', $this->row->rowcount, $this->row->editor, $this->row->checked_out_time); ?>
        <?php endif; ?>

        <?php if ($this->row->link) : ?>
        <a href="<?php echo $this->row->link; ?>">
            <?php echo htmlspecialchars($this->row->title, ENT_QUOTES, 'UTF-8');?></a>
        <?php  else :
        echo htmlspecialchars($this->row->title, ENT_QUOTES, 'UTF-8');
    endif; ?>
    </th>
    <td class="center">
        <?php echo MolajoHTML::_('jgrid.published', $this->row->state, $this->row->rowcount, '', false); ?>
    </td>
    <td class="center">
        <?php echo MolajoHTML::_('date', $this->row->created, 'Y-m-d H:i:s'); ?>
    </td>
    <td class="center">
        <?php echo $this->row->author_name;?>
    </td>
</tr>