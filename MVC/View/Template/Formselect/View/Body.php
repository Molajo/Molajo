<?php
/**
 * Formselect Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die; ?>
<option value="<?php echo $this->row->id; ?>"<?php echo $this->row->selected; ?>><?php echo $this->row->value; ?></option>
