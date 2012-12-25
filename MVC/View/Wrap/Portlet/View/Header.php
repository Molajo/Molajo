<?php
/**
 * @package    Niambie
 * @copyright  2013 Amy Stephen. All rights reserved.
 * @license    MIT
 */
defined('NIAMBIE') or die;  ?>
<div class="portlet-header">
<?php if ($this->parameters['criteria_title'] == '') {
} else { ?>
    <h4><?php echo $this->parameters['criteria_title']; ?></h4>
<?php } ?>
</div>
<div class="portlet-content">
