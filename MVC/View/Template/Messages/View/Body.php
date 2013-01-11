<?php
/**
 * Messages Template View
 *
 * @package      Niambie
 * @license      MIT
 * @copyright    2013 Amy Stephen. All rights reserved.
 */
use Molajo\Service\Services;

defined('NIAMBIE') or die; ?>
<div class="<?php echo $this->row->class; ?>">
    <?php echo $this->row->message; ?>
    <a class="close" href="<?php echo $this->row->action; ?>#">&times;</a>
    <h4 class="alert-heading"><?php echo $this->row->heading; ?></h4>
</div>
