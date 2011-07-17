<?php
/**
 * @package     Molajo
 * @subpackage  Layout
 * @copyright   Copyright (C) 2011 Amy Stephen. All rights reserved.
 * @license     GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die; ?>

<input type="text"
       name="<?php echo $this->row->name; ?>"
       id="<?php echo $this->row->id; ?>"
       value="<?php echo htmlspecialchars($this->row->prefix, ENT_COMPAT, 'UTF-8'); ?>"
       <?php if (isset($this->row->class)) { echo $this->row->class; } ?>
       <?php if (isset($this->row->disabled)) { echo $this->row->disabled; } ?>
       <?php if (isset($this->row->readonly)) { echo $this->row->readonly; } ?>
       <?php if (isset($this->row->onchange)) { echo $this->row->onchange; } ?>
       <?php if (isset($this->row->maxLength)) { echo $this->row->maxLength; } ?>
       <?php if (isset($this->row->class)) { echo $this->row->class; } ?>
       <?php if (isset($this->row->class)) { echo $this->row->class; } ?>
       <?php if (isset($this->row->class)) { echo $this->row->class; } ?>
            echo $this->row->disabled;


$this->row->readonly.$this->row->onchange.$this->row->maxLength;
        ?>"
				' value="'.htmlspecialchars($prefix, ENT_COMPAT, 'UTF-8').'"' .
				$class.$disabled.$readonly.$onchange.$maxLength.'/>';