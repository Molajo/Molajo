<?php

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined("MOLAJO") or die;
if ($this->row->new_fieldset == '1') {
?>
    </ol>
</fieldset>
<fieldset class="two-up">
    <legend><?php echo $this->row->tab_fieldset_title; ?></legend>
    <p><?php echo $this->row->tab_fieldset_description; ?></p>
    <ol>
<?php } ?>
        <li>
            <include:template name=<?php echo $this->row->view; ?> parameter_np=<?php echo $this->row->name; ?>/>
        </li>
