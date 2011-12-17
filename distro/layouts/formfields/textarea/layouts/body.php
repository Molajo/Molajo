<?php
/**
 * @package     Molajo
 * @subpackage  Layout
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
    <span>
<?php } ?>
    <textarea
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
            <?php echo $this->row->cols; ?>
            <?php echo $this->row->rows; ?>
            <?php echo $this->row->required; ?>
            <?php echo $this->row->maxlength; ?>
            <?php echo $this->row->readonly; ?>
            <?php echo $this->row->disabled; ?>
            <?php echo $this->row->placeholder; ?>
            <?php echo $this->row->autofocus; ?>
            <?php if ($this->row->onchange == "") {
    } else {
        echo ' onchange="' . (string)$this->row->onchange . '"';
    } ?>
            >
        <?php echo htmlspecialchars($this->row->value, ENT_COMPAT, 'UTF-8'); ?>
    </textarea>
<?php if ($this->row->label == "") {
} else {
    ?>
	</span>
</label>
<?php }