<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Cristina Solana, Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>
<input
   type="text"
   name="<?php echo $this->row->name; ?>"
   id="<?php echo $this->row->id; ?>"
   value="<?php echo htmlspecialchars($this->row->value, ENT_COMPAT, 'UTF-8'); ?>"
   <?php if ($this->row->class == "") { } else { echo ' class="'.$this->row->class.'"'; } ?>
   <?php if ($this->row->enabled === true) { } else { echo ' disabled="disabled"'; } ?>
   <?php if ($this->row->readonly === true) { echo ' readonly="readonly"'; } ?>
   <?php if ($this->row->onchange == "") { } else { echo $this->row->onchange; } ?>
/>