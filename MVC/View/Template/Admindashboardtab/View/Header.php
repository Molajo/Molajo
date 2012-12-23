<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;

$this->row->new_fieldset = '0';
?>
<h2>
    <span>
        <em><?php echo 'Articles Configuration'; ?></em>
        <strong><?php echo $this->row->tab_title; ?></strong>
    </span>
</h2>
<p class="tab-description"><?php echo $this->row->tab_description; ?></p>
<fieldset class="two-up">
    <legend><?php echo $this->row->tab_fieldset_title; ?></legend>
    <p class="fieldset-description"><?php echo $this->row->tab_fieldset_description; ?></p>
    <ol>
