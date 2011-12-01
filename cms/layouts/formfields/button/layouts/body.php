<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<button
        type="<?php echo $this->row->type; ?>"
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
        value="<?php echo htmlspecialchars($this->row->value, ENT_COMPAT, 'UTF-8'); ?>"
        <?php echo $this->row->disabled; ?>
        <?php if ($this->row->onclick == "") {
} else {
    echo ' onclick="' . (string)$this->row->onclick . '"';
} ?>
        />