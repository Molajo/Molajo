<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;
$this->row->new_fieldset = '0';
if ($this->row->tab_fieldset_title == 'Main') {
    $class = '';
} else {
    $class = 'two-up';
}
?>
<h2>
    <span>
        <em><?php echo 'Articles Edit'; ?></em>
        <strong><?php echo $this->row->tab_title; ?></strong>
    </span>
</h2>
<p class="tab-description"><?php echo $this->row->tab_description; ?></p>

 <fieldset class="<?php echo $class; ?>">

     <legend><?php echo $this->row->tab_fieldset_title; ?></legend>
     <p><?php echo $this->row->tab_fieldset_description; ?></p>
     <ol>
