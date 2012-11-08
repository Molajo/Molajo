<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die; ?>
<div class="configuration-header">
    <header>
		<h3><?php echo $this->row->page_title_extended; ?></h3>
		<?php if ($this->parameters['application_help'] == 1) { ?>
			<h6><?php echo $this->row->page_description; ?></h6>
		<?php } ?>
	</header>
    <section>
        <ul class="inline-list right">
            <li><a href="#"><strong><?php echo Services::Language()->translate('Reset', 'Reset'); ?></strong></a></li>
            <li><a href="#" class="button success radius"><?php echo Services::Language()->translate('Save', 'Save'); ?></a></li>
        </ul>
    </section>
</div>
<div class="configuration-body">
