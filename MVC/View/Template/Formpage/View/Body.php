<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined("MOLAJO") or die;

if ($this->row->page_first_row == 1) {
} elseif ($this->row->new_fieldset == 1) { ?>
</fieldset>
<?php }

if ($this->row->page_first_row == 1) {
} elseif ($this->row->page_subtitle_first_row == 1) { ?>
</div>
<?php }

if ($this->row->page_subtitle_first_row == 1) { ?>
<hr />
<div id="<?php echo $this->row->page_subtitle; ?>" class="subtitle radius">
    <h4><?php echo $this->row->page_subtitle; ?></h4>
    <?php if ($this->parameters['application_help'] == 1) { ?>
        <h6><?php echo $this->row->page_subtitle_description; ?></h6>
    <?php } ?>
</div>
<hr />
<div class="configuration-body">
<?php }

if ($this->row->new_fieldset == 1) { ?>
<fieldset class="configuration">
	<legend class="configuration"><?php echo $this->row->fieldset_title; ?></legend>
	<?php if ($this->parameters['application_help'] == 1) { ?>
			<p><?php echo $this->row->fieldset_description; ?></p>
	<?php }
} ?>
<include:template name=<?php echo $this->row->view; ?> parameter_np=<?php echo $this->row->name; ?>/>
