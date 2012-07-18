<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$action = Services::Registry()->get('Triggerdata', 'full_page_url');

$class = 'alert-box';
if ($this->row->type == MESSAGE_TYPE_SUCCESS) {
    $heading = Services::Language()->translate('Success');
    $class .= ' success';

} elseif ($this->row->type == MESSAGE_TYPE_WARNING) {
    $heading = Services::Language()->translate('Warning');
    $class .= ' warning';

} elseif ($this->row->type == MESSAGE_TYPE_ERROR) {
    $heading = Services::Language()->translate('Error');
    $class .= ' alert';

} else {
// defaults MESSAGE_TYPE_INFORMATION
    $heading = Services::Language()->translate('Information');
    $class .= ' secondary';
}
?>
<div class="<?php echo $class; ?>">
    <?php echo $this->row->message; ?>
    <a class="close" href="<?php echo $action; ?>">&times;</a>
    <h4 class="alert-heading"><?php echo $heading; ?></h4>
</div>
