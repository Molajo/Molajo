<?php
/**
 * @package   Molajo
 * @copyright 2012 Amy Stephen. All rights reserved.
 * @license   GNU General Public License Version 2, or later http://www.gnu.org/licenses/gpl.html
 */
defined('MOLAJO') or die;
use Molajo\Service\Services;

$class = 'alert-box';
if ($this->row->type == MESSAGE_TYPE_SUCCESS) {
	$heading = Services::Language()->translate('Success');
	$class .= ' success';

} elseif ($this->row->type == MESSAGE_TYPE_WARNING) {
	$heading = Services::Language()->translate('Warning');
	$class .= ' warning';

} elseif ($this->row->type == MESSAGE_TYPE_ERROR) {
	$heading = Services::Language()->translate('Error');
	$class .= ' error';

} else {
// defaults MESSAGE_TYPE_INFORMATION
	$heading = Services::Language()->translate('Information');
}
?>
<div class="<?php echo $class; ?>">
	<?php echo $this->row->message; ?>
	<a class="close" href="">&times;</a>
	<h4 class="alert-heading"><?php echo $heading; ?></h4>
</div>
