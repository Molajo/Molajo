<?php
/**
 * Forminput Template View
 *
 * @package      Molajo
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('MOLAJO') or die;
$tooltip_css_class = 'has-tip';
if ($this->row->type == 'hidden') {
} else {
    ?>
<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>">
<label for="<?php echo $this->row->id; ?>"><?php echo $this->row->label; ?></label></span>
<?php } ?>
<input
