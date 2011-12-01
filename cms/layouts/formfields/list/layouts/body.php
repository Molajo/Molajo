<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */

/*	if ((string) $this->element['readonly'] == 'true') {
            $html[] = MolajoHTML::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
            $html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
        } */

defined('MOLAJO') or die; ?>
<option
        type="<?php echo $this->row->type; ?>"
        value="<?php echo htmlspecialchars($this->row->value, ENT_COMPAT, 'UTF-8'); ?>"
        <?php if ($this->row->id == "") {
} else {
    echo ' id="' . htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8') . '"';
} ?>
        <?php if ($this->row->class == "") {
} else {
    echo ' class="' . htmlspecialchars($this->row->class, ENT_COMPAT, 'UTF-8') . '"';
} ?>
        <?php if ($this->row->name == "") {
} else {
    echo ' name="' . $this->row->name . '"';
} ?>
        <?php echo $this->row->readonly; ?>
        <?php echo $this->row->disabled; ?>
        <?php echo $this->row->multiple; ?>
        <?php echo $this->row->autofocus; ?>
        />