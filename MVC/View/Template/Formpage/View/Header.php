<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
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
            <li class="small-device"><h6 class="small-device"><?php echo $this->row->page_title_extended; ?></strong></h6>
            <li><a href="#"><strong><?php echo Services::Language()->translate('Reset', 'Reset'); ?></strong></a></li>
            <li><a href="#" class="button success radius"><?php echo Services::Language()->translate('Save', 'Save'); ?></a></li>
        </ul>
    </section>
</header>
<div class="configuration-body">
