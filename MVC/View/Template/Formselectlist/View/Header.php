<?php
/**
 * Formselectlist Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
defined('NIAMBIE') or die; ?>
<select <?php echo $this->row->multiple; ?>name="<?php echo $this->row->listname; ?>" class="inputbox">
<?php if ($this->row->multiple == '') { ?>
    <option value=""><?php echo Services::Language()->translate(
        'SELECT_' . strtoupper($this->row->listname)
    ); ?></option>
    <?php } else { ?>
    <option value=""><?php echo Services::Language()->translate('No selection'); ?></option>
    <option value="#"><?php echo Services::Language()->translate('Select all'); ?></option>
<?php }
