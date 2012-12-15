<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('NIAMBIE') or die;  ?>
<fieldset>
<?php if ($this->parameters['criteria_title'] == '') {
} else { ?>
    <legend><?php echo $this->parameters['criteria_title']; ?></legend>
<?php }
