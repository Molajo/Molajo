<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<li>
	<input id="<?php echo $this->row->id; ?>"<?php echo $this->row->checked; ?> name="<?php echo $this->row->name; ?>" type="radio">
		<label for="<?php echo $this->row->id; ?>"><?php echo $this->row->id_label; ?></label>
</li>
