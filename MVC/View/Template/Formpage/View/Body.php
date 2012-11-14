<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die;

if ($this->row->page_first_row == 1) {
} elseif ($this->row->new_fieldset == 1) { ?>
</fieldset>
<?php }

if ($this->row->new_fieldset == 1) { ?>
<fieldset class="configuration">
	<legend class="configuration"><?php echo $this->row->fieldset_title; ?></legend>
	<?php if ($this->parameters['application_help'] == 1) { ?>
			<p><?php echo $this->row->fieldset_description; ?></p>
	<?php }
} ?>
<include:template name=<?php echo $this->row->view; ?> parameter_np=<?php echo $this->row->name; ?>/>
