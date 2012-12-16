<?php
/**
 * @package    Niambie
 * @copyright  2012 Amy Stephen. All rights reserved.
 * @license    MIT, see License folder
 */
defined('NIAMBIE') or die; ?>
<li>
    <?php echo '<strong>' . $this->row->code . ' ' . $this->row->title . ':</strong> ' . $this->row->content_text; ?>
</li>
