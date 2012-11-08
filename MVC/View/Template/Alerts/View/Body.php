<?php

/**
 * @package    Molajo
 * @copyright  2012 Individual Molajo Contributors. All rights reserved.
 * @license    GNU GPL v 2, or later and MIT, see License folder
 */
defined('MOLAJO') or die; ?>
<div class="<?php echo $this->row->class; ?>">
    <?php echo $this->row->message; ?>
    <a class="close" href="<?php echo $this->row->action; ?>#">&times;</a>
    <h4 class="<?php echo $this->parameters['css_class']; ?>"><?php echo $this->row->heading; ?></h4>
</div>
