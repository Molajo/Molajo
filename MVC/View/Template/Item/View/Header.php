<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>

<include:ui name=button-group button_group_shape=radius class=listbox button_group_array=<?php echo trim($this->row->edit_button) . trim($this->row->delete_button); ?>/>

<p id="modified"></p>
