<?php
use Molajo\Service\Services;
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;

$this->row->new_fieldset = '0';
?>
<h2>
    <span>
        <em><?php echo Services::Registry()->get('Plugindata', 'heading1'); ?> <?php echo Services::Registry()->get('Plugindata', 'heading2'); ?></em>
        <strong><?php echo $this->row->tab_title; ?></strong>
    </span>
</h2>
<?php if (Services::Registry()->get('Parameters', 'application_help') == 1) { ?>
    <p class="tab-description"><?php echo $this->row->tab_description; ?></p>
<?php } ?>
<fieldset class="two-up">
    <legend><?php echo $this->row->tab_fieldset_title; ?></legend>
    <?php if (Services::Registry()->get('Parameters', 'application_help') == 1) { ?>
        <p class="fieldset-description"><?php echo $this->row->tab_fieldset_description; ?></p>
    <?php } ?>
    <ol>
