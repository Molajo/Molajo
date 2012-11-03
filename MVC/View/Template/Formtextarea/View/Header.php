<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * echo Services::Language()->translate('No textareaion')
 */
defined('MOLAJO') or die;
$tooltip_css_class = 'has-tip';
?>
<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>"><label for="<?php echo $this->row->id; ?>"><?php echo $this->row->label; ?></label></span><textarea <?php
if ($this->row->placeholder == '') {
} else {
	echo 'placeholder' . '="' . $this->row->placeholder .'" ';
} ?>
