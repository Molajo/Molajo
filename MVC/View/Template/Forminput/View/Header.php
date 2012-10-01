<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 * echo Services::Language()->translate('No inpution')
 */
defined('MOLAJO') or die;
if ($this->row->type == 'hidden') {
} else {
?>
<label for="<?php echo $this->row->id; ?>"><?php echo $this->row->label; ?></label>
<?php } ?>
<input
