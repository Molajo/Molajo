<?php
/**
 * Formradio Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;
$tooltip_css_class = 'has-tip';
?>
<li>
    <input id="<?php echo $this->row->id; ?>"<?php echo $this->row->checked; ?> name="<?php echo $this->row->name; ?>"
           type="radio">
    <label for="<?php echo $this->row->id; ?>"><?php echo $this->row->id_label; ?></label>
</li>
