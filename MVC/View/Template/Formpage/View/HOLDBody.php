<?php
use Molajo\Service\Services;

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die;

$first_row = $this->row->page_first_row;

if ($first_row == 1) {

/** End Last Fieldset */
} elseif ($this->row->new_fieldset == 1) { ?>
    	    </fieldset>
        </div>
<?php
	if ($this->row->fieldset_column == 1) {  ?>
    </div>
<?php }
}

/** Process NEW Recordset */
if ($this->row->new_fieldset == 1) {

	if ($first_row == 1 || $this->row->fieldset_column  == 1) {  ?>
    <div class="left-configuration-row">
		<div class="left-configuration-column">
			<fieldset class="configuration">

	<?php } else { ?>
		<div class="right-configuration-column">
			<fieldset class="configuration">
	<?php }  ?>

	<legend class="configuration"><?php echo $this->row->fieldset_title; ?></legend>
	<?php if ($this->parameters['application_help'] == 1) { ?>
			<p><?php echo $this->row->fieldset_description; ?></p>
	<?php }
} ?>
<include:template name=<?php echo $this->row->view; ?> parameter_np=<?php echo $this->row->name; ?>/>
