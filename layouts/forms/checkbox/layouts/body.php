<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen, Cristina Solana. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<?php if ($this->row->label == "") {
} else { ?>
<label
    class="hasTip"
    <?php if ($this->row->id == "") { } else { echo ' for="'.htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8').'"'; } ?>
    <?php if ($this->row->description == "") { } else { echo ' name="'.htmlspecialchars(JText::_($this->row->description)).'"'; } ?>
    <?php echo JText::_(($this->row->label), ENT_COMPAT, 'UTF-8'); ?>
    >
	<span>
<?php } ?>
		<input
		   type="checkbox"
		   <?php if ($this->row->id == "") { } else { echo ' id="'.htmlspecialchars($this->row->id, ENT_COMPAT, 'UTF-8').'"'; } ?>
		   <?php if ($this->row->name == "") { } else { echo ' name="'.$this->row->name.'"'; } ?>
		   <?php if ($this->row->checked === true) {
					echo ' name="'.$this->row->name.'"';
					echo ' value="'.htmlspecialchars($this->row->value, ENT_COMPAT, 'UTF-8').'"';
			} ?>
		   <?php if ($this->row->required === true) { } else { $this->row->class .= ' required'; } ?>
		   <?php if ($this->row->class == "") { } else { echo ' class="'.htmlspecialchars($this->row->class, ENT_COMPAT, 'UTF-8').'"'; } ?>
		   <?php if ($this->row->disabled === false) { } else { echo ' disabled="disabled"'; } ?>
		   <?php if ($this->row->multiple === false) { } else { echo ' disabled="multiple"'; } ?>
		   <?php if ($this->row->name == "") { } else { echo ' name="'.$this->row->name.'"'; } ?>
		   <?php if ($this->row->onclick == "") { } else { echo ' onclick="'.(string) $this->row->onclick.'"'; } ?>
		   <?php if ($this->row->readonly === true) { echo ' readonly="readonly"'; } ?>
		/>
<?php if ($this->row->label == "") {
} else { ?>
	</span>
</label>
<?php }