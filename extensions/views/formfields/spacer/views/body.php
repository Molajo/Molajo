<?php
/**
 * @package     Molajo
 * @subpackage  View
 * @copyright   Copyright (C) 2012 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<?php if ($this->row->label == "") {
} else {
    ?>
<label
        class="hasTip"
    <?php if ($this->row->id == "") {
} else {
    echo ' for="' . htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8') . '"';
} ?>
    <?php if ($this->row->description == "") {
} else {
    echo ' name="' . htmlspecialchars(MolajoTextHelper::_($this->row->description)) . '"';
} ?>
    <?php echo MolajoTextHelper::_(($this->row->label), ENT_COMPAT, 'UTF-8'); ?>
        >
<?php } ?>
    <input
            type="<?php echo $this->row->type; ?>"
            <?php if ($this->row->id == "") {
    } else {
        echo ' id="' . htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8') . '"';
    } ?>
            <?php if ($this->row->name == "") {
    } else {
        echo ' name="' . $this->row->name . '"';
    } ?>
            value="<?php echo htmlspecialchars($this->row->value, ENT_COMPAT, 'UTF-8'); ?>"
            <?php if ($this->row->required === true) {
    } else {
        $this->row->class .= ' required';
    } ?>
            <?php if ($this->row->class == "") {
    } else {
        echo ' class="' . htmlspecialchars($this->row->class, ENT_COMPAT, 'UTF-8') . '"';
    } ?>
            <?php if ($this->row->disabled === false) {
    } else {
        echo ' disabled="disabled"';
    } ?>
            <?php if ((int)$this->row->maxlength == 0) {
    } else {
        echo ' maxlength="' . (int)$this->row->maxlength . '"';
    } ?>
            <?php if ($this->row->multiple === false) {
    } else {
        echo ' disabled="multiple"';
    } ?>
            <?php if ($this->row->name == "") {
    } else {
        echo ' name="' . $this->row->name . '"';
    } ?>
            <?php if ($this->row->onchange == "") {
    } else {
        echo ' onchange="' . (string)$this->row->onchange . '"';
    } ?>
            <?php if ($this->row->readonly === true) {
        echo ' readonly="readonly"';
    } ?>
            <?php if ((int)$this->row->size == 0) {
    } else {
        echo ' size="' . (int)$this->row->size . '"';
    } ?>
            />
<?php if ($this->row->label == "") {
} else {
    ?>
</label>
<?php }