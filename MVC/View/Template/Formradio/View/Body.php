<?php

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;
$tooltip_css_class = 'has-tip';
?>
<li>
    <input id="<?php echo $this->row->id; ?>"<?php echo $this->row->checked; ?> name="<?php echo $this->row->name; ?>" type="radio">
        <label for="<?php echo $this->row->id; ?>"><?php echo $this->row->id_label; ?></label>
</li>
