<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
use Molajo\Service\Services;

if ($this->row->type == MESSAGE_TYPE_SUCCESS) {
	$heading = Services::Language()->translate('Success');
	$class = 'alert alert-success';

} elseif ($this->row->type == MESSAGE_TYPE_WARNING) {
	$heading = Services::Language()->translate('Warning');
	$class = 'alert alert-warning';

} elseif ($this->row->type == MESSAGE_TYPE_ERROR) {
	$heading = Services::Language()->translate('Error');
	$class = 'alert alert-error';

} else {
// defaults MESSAGE_TYPE_INFORMATION
	$heading = Services::Language()->translate('Information');
	$class = 'alert alert-info';
}
?>
<div class="<?php echo $class; ?>">
	<a class="close" data-dismiss="alert" href="#">×</a>
	<h4 class="alert-heading"><?php echo $heading; ?></h4>
	<?php echo $this->row->message; ?>
</div>
