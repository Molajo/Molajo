<?php
/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;  ?>
<fieldset>
<?php if ($this->parameters['criteria_title'] == '') {
} else { ?>
    <legend><?php echo $this->parameters['criteria_title']; ?></legend>
<?php }
