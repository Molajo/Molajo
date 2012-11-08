<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<select <?php echo $this->row->multiple; ?> name="<?php echo $this->row->listname; ?>" class="inputbox" onchange="javascript: submitform()">
<?php if ($this->row->multiple == '') { ?>
    <option value=""><?php echo Services::Language()->translate('SELECT_' . strtoupper($this->row->listname)); ?></option>
<?php } else { ?>
    <option value=""><?php echo Services::Language()->translate('No selection'); ?></option>
    <option value="#"><?php echo Services::Language()->translate('Select all'); ?></option>
<?php }
