<?php
use Molajo\Service\Services;

/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined("MOLAJO") or die; ?>
<header class="configuration-header">
    <header class="configuration-header-header">
        <h3><?php echo $this->row->page_title_extended; ?></h3>
        <?php if ($this->parameters['application_help'] == 1) { ?>
        <h6><?php echo $this->row->page_description; ?></h6>
        <?php } ?>
    </header>
    <section class="configuration-header-section">
        <ul class="inline-list right">
            <li><a href="#"><strong><?php echo Services::Language()->translate('Reset', 'Reset'); ?></strong></a></li>
            <li><a href="#" class="button success radius"><?php echo Services::Language()->translate('Save', 'Save'); ?></a></li>
        </ul>
    </section>
</header>
<?php if ($this->row->page_subtitle_first_row == 1) {
} else { ?>
<div class="configuration-body">
<?php }
