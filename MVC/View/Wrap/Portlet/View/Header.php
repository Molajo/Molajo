<?php
/**
 * @package    Molajo
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die;  ?>
<div class="portlet-header">
<?php if ($this->parameters['criteria_title'] == '') {
} else { ?>
    <h4><?php echo $this->parameters['criteria_title']; ?></h4>
<?php } ?>
</div>
<div class="portlet-content">
