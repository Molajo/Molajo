<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 * echo Services::Language()->translate('No inpution')
 */
defined('NIAMBIE') or die;
$tooltip_css_class = 'has-tip';
if ($this->row->type == 'hidden') {
} else {
?>
<span class="<?php echo $tooltip_css_class; ?>" title="<?php echo $this->row->tooltip; ?>">
<label for="<?php echo $this->row->id; ?>"><?php echo $this->row->label; ?></label></span>
<?php } ?>
<input
