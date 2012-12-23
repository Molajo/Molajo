<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die; ?>
<option value="<?php echo $this->row->id; ?>"<?php echo $this->row->selected; ?>><?php echo $this->row->value; ?></option>
